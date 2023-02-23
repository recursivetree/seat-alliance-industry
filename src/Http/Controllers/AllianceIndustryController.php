<?php

namespace RecursiveTree\Seat\AllianceIndustry\Http\Controllers;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use RecursiveTree\Seat\AllianceIndustry\Jobs\SendOrderNotifications;
use RecursiveTree\Seat\AllianceIndustry\Models\Order;
use RecursiveTree\Seat\AllianceIndustry\Models\Delivery;
use RecursiveTree\Seat\AllianceIndustry\Prices\AllianceIndustryPriceSettings;
use RecursiveTree\Seat\TreeLib\Helpers\SeatInventoryPluginHelper;
use RecursiveTree\Seat\TreeLib\Items\EveItem;
use RecursiveTree\Seat\TreeLib\Parser\Parser;
use Seat\Eveapi\Models\Universe\UniverseStation;
use Seat\Eveapi\Models\Universe\UniverseStructure;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Gate;


class AllianceIndustryController extends Controller
{
    public function orders(){

        $orders = Order::with("deliveries")->where("completed",false)->get()->filter(function ($order){
            return $order->assignedQuantity() < $order->quantity;
        });

        $personalOrders = Order::where("user_id",auth()->user()->id)->get();

        return view("allianceindustry::orders", compact("orders","personalOrders"));
    }

    public function createOrder(){
        $stations = UniverseStation::all();
        $structures = UniverseStructure::all();

        $mpp = AllianceIndustrySettings::$MINIMUM_PROFIT_PERCENTAGE->get(2.5);

        $location_id = AllianceIndustrySettings::$DEFAULT_ORDER_LOCATION->get(60003760);//jita

        return view("allianceindustry::createOrder",compact("stations", "structures","mpp","location_id"));
    }

    public function submitOrder(Request $request){
        $request->validate([
            "items"=>"required|string",
            "profit"=>"required|numeric|min:0",
            "days"=>"required|integer|min:1",
            "location"=>"required|integer",
            "addProfitToManualPrices"=>"nullable|in:on",
            "addToSeatInventory"=>"nullable|in:on",
            "priority"=>"required|integer"
        ]);

        $mpp = AllianceIndustrySettings::$MINIMUM_PROFIT_PERCENTAGE->get(2.5);
        if($request->profit < $mpp){
            $request->session()->flash("error","The minimal profit can't be lower than $mpp%");
            return redirect()->route("allianceindustry.createOrder");
        }

        if(!(UniverseStructure::where("structure_id",$request->location)->exists()||UniverseStation::where("station_id",$request->location)->exists())) {
            $request->session()->flash("error","Could not find structure/station.");
            return redirect()->route("allianceindustry.orders");
        }

        //parse items
        $parser_result = Parser::parseItems($request->items);

        //check item count, don't request prices without any items
        if($parser_result == null || $parser_result->items->isEmpty()){
            $request->session()->flash("warning","You need to add at least 1 item to the delivery");
            return redirect()->route("allianceindustry.orders");
        }

        $appraised_items = config('allianceindustry.config.priceProvider')::getPrices($parser_result->items, new AllianceIndustryPriceSettings());

        $now = now();
        $produce_until = now()->addDays($request->days);
        $price_modifier = (1+(floatval($request->profit)/100.0));
        $prohibitManualPricesBelowValue = !AllianceIndustrySettings::$ALLOW_PRICES_BELOW_AUTOMATIC->get(false);
        $addToSeatInventory = $request->addToSeatInventory !== null;
        $addProfitToManualPrice = $request->addProfitToManualPrices == "on";
        if(!SeatInventoryPluginHelper::pluginIsAvailable()){
            $addToSeatInventory = false;
        }

        foreach ($appraised_items as $item){
            $order = new Order();

            if($item->manualPrice !== null && $item->manualPrice < $item->marketPrice && $prohibitManualPricesBelowValue){
                $item->price = $item->marketPrice;
            }

            if($item->manualPrice == null || $addProfitToManualPrice){
                $item->price *= $price_modifier;
            }

            $order->type_id = $item->typeModel->typeID;
            $order->quantity = $item->amount;
            $order->user_id = auth()->user()->id;
            $order->unit_price = $item->price;
            $order->location_id = $request->location;
            $order->created_at = $now;
            $order->produce_until = $produce_until;
            $order->add_seat_inventory = $addToSeatInventory;
            $order->profit = floatval($request->profit);
            $order->priority = $request->priority;

            $order->save();
        }

        //send notification that orders have been put up. We don't do it in an observer so it only gets triggered once
        SendOrderNotifications::dispatch()->onQueue('notifications');

        $request->session()->flash("success","Successfully added new order");
        return redirect()->route("allianceindustry.orders");
    }

    public function updateOrderPrice(Request $request){
        $request->validate([
            "order"=>"required|integer"
        ]);
        $order = Order::find($request->order);
        if(!$order){
            $request->session()->flash("error","The order wasn't found");
            return redirect()->back();
        }

        Gate::authorize("allianceindustry.same-user",$order->user_id);

        $profit_multiplier = 1+($order->profit/100.0);

        $item_list = collect([new EveItem($order->type)]);
        $item = config('allianceindustry.config.priceProvider')::getPrices($item_list, new AllianceIndustryPriceSettings())->first();
        $price = $item->price * $profit_multiplier;

        $order->unit_price = $price;
        $order->save();

        $request->session()->flash("success","Updated the price!");
        return redirect()->back();
    }

    public function orderDetails($id, Request $request){
        $order = Order::with("deliveries")->find($id);

        if(!$order){
            $request->session()->flash("error","Could not find order");
            return redirect()->route("allianceindustry.orders");
        }

        return view("allianceindustry::orderDetails",compact("order"));
    }

    public function addDelivery($orderId, Request $request){
        $request->validate([
            "quantity"=>"required|integer"
        ]);

        $order = Order::find($orderId);
        if(!$order){
            $request->session()->flash("error","Could not find order");
            return redirect()->route("allianceindustry.orders");
        }

        //quantity > 0
        if($request->quantity < 1){
            $request->session()->flash("error","Quantity must be larger than 0");
            return redirect()->route("allianceindustry.orders");
        }

        //quantity <= max remaining
        if($request->quantity > $order->quantity - $order->assignedQuantity()){
            $request->session()->flash("error","Quantity must be smaller than the remaining quantity");
            return redirect()->route("allianceindustry.orders");
        }

        $delivery = new Delivery();
        $delivery->order_id = $order->id;
        $delivery->user_id = auth()->user()->id;
        $delivery->quantity = $request->quantity;
        $delivery->completed = false;
        $delivery->accepted = now();

        $delivery->save();

        $request->session()->flash("success","Successfully added new delivery");
        return redirect()->back();
    }

    public function setDeliveryState($orderId,Request $request){
        $request->validate([
            "delivery"=>"required|integer",
            "completed"=>"required|boolean"
        ]);

        $delivery = Delivery::find($request->delivery);

        Gate::authorize("allianceindustry.same-user",$delivery->user_id);

        if($request->completed){
            $delivery->completed_at = now();
            $delivery->completed = true;
        } else {
            $delivery->completed_at = null;
            $delivery->completed = false;
        }
        $delivery->save();

        return redirect()->back();
    }

    public function deleteDelivery($orderId, Request $request){
        $request->validate([
            "delivery"=>"required|integer",
        ]);

        $delivery = Delivery::find($request->delivery);

        if($delivery) {
            Gate::authorize("allianceindustry.same-user", $delivery->user_id);

            if($delivery->completed){
                Gate::authorize("allianceindustry.admin");
            }

            $delivery->delete();

            $request->session()->flash("success","Successfully removed delivery");
        } else {
            $request->session()->flash("error","Could not find delivery");
        }

        return redirect()->back();
    }

    public function deliveries(){
        $user_id = auth()->user()->id;

        $deliveries = Delivery::with("order")->where("user_id",$user_id)->get();

        return view("allianceindustry::deliveries",compact("deliveries"));
    }

    public function deleteOrder(Request $request){
        $request->validate([
            "order"=>"required|integer"
        ]);

        $order = Order::find($request->order);
        if(!$order){
            $request->session()->flash("error","Could not find order");
            return redirect()->route("allianceindustry.orders");
        }

        Gate::authorize("allianceindustry.same-user",$order->user_id);

        if(!$order->deliveries->isEmpty() && !$order->completed && !auth()->user()->can("allianceindustry.admin")){
            $request->session()->flash("error","You cannot delete orders that people are currently manufacturing!");
            return redirect()->route("allianceindustry.orders");
        }

        $order->delete();

        $request->session()->flash("success","Successfully closed order!");
        return redirect()->route("allianceindustry.orders");
    }

    public function about(){
        return view("allianceindustry::about");
    }

    public function settings(){
        $stations = UniverseStation::all();
        $structures = UniverseStructure::all();

        $defaultOrderLocation = AllianceIndustrySettings::$DEFAULT_ORDER_LOCATION->get(60003760);
        $marketHub = AllianceIndustrySettings::$MARKET_HUB->get("jita");
        $mpp = AllianceIndustrySettings::$MINIMUM_PROFIT_PERCENTAGE->get(2.5);
        $priceType = AllianceIndustrySettings::$PRICE_TYPE->get("buy");
        $orderCreationPingRoles =  implode(" ", AllianceIndustrySettings::$ORDER_CREATION_PING_ROLES->get([]));
        $allowPriceBelowAutomatic = AllianceIndustrySettings::$ALLOW_PRICES_BELOW_AUTOMATIC->get(false);

        return view("allianceindustry::settings", compact("marketHub","mpp","priceType", "orderCreationPingRoles","allowPriceBelowAutomatic","stations","structures","defaultOrderLocation"));
    }

    public function saveSettings(Request $request){
        $request->validate([
            "market"=>"required|in:jita,perimeter,universe,amarr,dodixie,hek,rens",
            "pricetype"=>"required|in:sell,buy",
            "minimumprofitpercentage"=>"required|numeric|min:0",
            "pingRolesOrderCreation"=>"string|nullable",
            "allowPriceBelowAutomatic"=>"nullable|in:on",
            "defaultLocation"=>"required|integer"
        ]);

        $roles = [];
        if($request->pingRolesOrderCreation){
            //parse roles
            $roles = preg_replace('~\R~u', "\n", $request->pingRolesOrderCreation);
            $matches = [];
            preg_match_all("/\d+/m",$roles, $matches);
            $roles = $matches[0];
        }


        AllianceIndustrySettings::$MARKET_HUB->set($request->market);
        AllianceIndustrySettings::$MINIMUM_PROFIT_PERCENTAGE->set(floatval($request->minimumprofitpercentage));
        AllianceIndustrySettings::$PRICE_TYPE->set($request->pricetype);
        AllianceIndustrySettings::$ORDER_CREATION_PING_ROLES->set($roles);
        AllianceIndustrySettings::$ALLOW_PRICES_BELOW_AUTOMATIC->set(boolval($request->allowPriceBelowAutomatic));
        AllianceIndustrySettings::$DEFAULT_ORDER_LOCATION->set($request->defaultLocation);

        $request->session()->flash("success","Successfully saved settings");
        return redirect()->route("allianceindustry.settings");
    }

    public function deleteCompletedOrders(){
        $orders = Order::where("user_id",auth()->user()->id)->where("completed",true)->get();
        foreach ($orders as $order){
            $order->delete();
        }

        return redirect()->back();
    }
}