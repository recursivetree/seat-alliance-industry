<?php

namespace RecursiveTree\Seat\AllianceIndustry\Http\Controllers;


use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
use RecursiveTree\Seat\AllianceIndustry\Models\Order;
use RecursiveTree\Seat\AllianceIndustry\Models\Delivery;
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
        $orders = Order::where("completed",false)->get();

        $personalOrders = Order::where("user_id",auth()->user()->id)->get();

        return view("allianceindustry::orders", compact("orders","personalOrders"));
    }

    public function createOrder(){
        $stations = UniverseStation::all();
        $structures = UniverseStructure::all();

        $mpp = SettingHelper::getSetting("minimumProfitPercentage",2.5);

        return view("allianceindustry::createOrder",compact("stations", "structures","mpp"));
    }

    public function submitOrder(Request $request){
        $request->validate([
            "items"=>"required|string",
            "profit"=>"required|numeric|min:0",
            "days"=>"required|integer|min:1",
            "location"=>"required|integer"
        ]);

        $mpp = SettingHelper::getSetting("minimumProfitPercentage",2.5);
        if($request->profit < $mpp){
            $request->session()->flash("error","The minimal profit can't be lower than $mpp%");
            return redirect()->route("allianceindustry.createOrder");
        }


        if(!(UniverseStructure::where("structure_id",$request->location)->exists()||UniverseStation::where("station_id",$request->location)->exists())) {
            $request->session()->flash("error","Could not find structure/station.");
            return redirect()->route("allianceindustry.orders");
        }

        //parse items
        $multibuy = preg_replace('~\R~u', "\n", $request->items);
        $matches = [];
        preg_match_all("/^(?<item_name>[\w '-]+?)\s+x?(?<item_amount>\d+)(?:\s+-)*$/m",$multibuy, $matches);


        //get items
        $items = [];
        foreach (array_combine($matches["item_name"], $matches["item_amount"]) as $item=>$amount){
            $amount = intval($amount);
            if($amount<1) continue;

            $items[] = [
                "name"=>$item,
                "quantity"=>$amount
            ];
        }
        if(count($items)<1){
            $request->session()->flash("warning","You need to add at least 1 item to the delivery");
            return redirect()->route("allianceindustry.orders");
        }

        try {
            $market = SettingHelper::getSetting("marketHub","jita");

            $client = new Client([
                'timeout'  => 5.0,
            ]);
            $response = $client->request('POST', "https://evepraisal.com/appraisal/structured.json",[
                'json' => [
                    'market_name' => $market,
                    'persist' => 'false',
                    'items'=>$items,
                ]
            ]);
            //decode request
            $data = json_decode( $response->getBody());
        } catch (GuzzleException $e){
            $request->session()->flash("error","Failed to load market data.");
            return redirect()->route("allianceindustry.orders");
        }

        $now = now();
        $produce_until = now()->addDays($request->days);
        $priceType = SettingHelper::getSetting("priceType","buy");
        $price_modifier = (1+(floatval($request->profit)/100.0));

        foreach ($data->appraisal->items as $item){
            $order = new Order();

            if($priceType==="sell"){
                $unit_price = $item->prices->sell->percentile;
            } else {
                $unit_price = $item->prices->buy->percentile;
            }

            $order->type_id = $item->typeID;
            $order->quantity = $item->quantity;
            $order->user_id = auth()->user()->id;
            $order->unit_price = $unit_price * $price_modifier;
            $order->location_id = $request->location;
            $order->created_at = $now;
            $order->produce_until = $produce_until;

            $order->save();
        }

        $request->session()->flash("success","Successfully added new order");
        return redirect()->route("allianceindustry.orders");
    }

    public function orderDetails($id, Request $request){
        $order = Order::find($id);

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
        return redirect()->route("allianceindustry.orderDetails",$orderId);
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

        return redirect()->route("allianceindustry.orderDetails",$orderId);
    }

    public function deleteDelivery($orderId, Request $request){
        $request->validate([
            "delivery"=>"required|integer",
        ]);

        $delivery = Delivery::find($request->delivery);

        if($delivery) {
            Gate::authorize("allianceindustry.same-user", $delivery->user_id);
            $delivery->delete();

            $request->session()->flash("success","Successfully removed delivery");
        } else {
            $request->session()->flash("error","Could not find delivery");
        }

        return redirect()->route("allianceindustry.orderDetails",$orderId);
    }

    public function deliveries(){
        $user_id = auth()->user()->id;

        $deliveries = Delivery::where("user_id",$user_id)->get();

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
        $marketHub = SettingHelper::getSetting("marketHub","jita");
        $mpp = SettingHelper::getSetting("minimumProfitPercentage",2.5);
        $priceType = SettingHelper::getSetting("priceType","buy");
        $orderCreationPingRoles =  implode(" ", SettingHelper::getSetting("orderCreationPingRoles",[]));

        return view("allianceindustry::settings", compact("marketHub","mpp","priceType", "orderCreationPingRoles"));
    }

    public function saveSettings(Request $request){
        $request->validate([
            "market"=>"required|in:jita,perimeter,universe,amarr,dodixie,hek,rens",
            "pricetype"=>"required|in:sell,buy",
            "minimumprofitpercentage"=>"required|numeric|min:0",
            "pingRolesOrderCreation"=>"string|nullable"
        ]);

        $roles = [];
        if($request->pingRolesOrderCreation){
            //parse roles
            $roles = preg_replace('~\R~u', "\n", $request->pingRolesOrderCreation);
            $matches = [];
            preg_match_all("/\d+/m",$roles, $matches);
            $roles = $matches[0];
        }


        SettingHelper::setSetting("marketHub",$request->market);
        SettingHelper::setSetting("minimumProfitPercentage", floatval($request->minimumprofitpercentage));
        SettingHelper::setSetting("priceType",$request->pricetype);
        SettingHelper::setSetting("orderCreationPingRoles",$roles);

        $request->session()->flash("success","Successfully saved settings");
        return redirect()->route("allianceindustry.settings");
    }
}