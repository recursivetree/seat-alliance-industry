<table class="data-table table table-striped table-hover">
    <thead>
    <tr>
        @if($showOrder ?? false)
            <th>Order</th>
        @endif
        <th>Amount</th>
        <th>Completed</th>
        @if($showOrder ?? false)
            <th>Unit Price</th>
        @endif
        <th>Total Price</th>
        <th>Accepted</th>
        @if($showOrder ?? false)
            <th>Ordered By</th>
        @endif
        <th>Character</th>
        <th>Corporation</th>
        <th>Alliance</th>
            <th>Location</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($deliveries as $delivery)
        <tr>
            @if($showOrder ?? false)
                <td data-order="{{ $delivery->order->type->typeID }}" data-filter="{{ $delivery->order->type->typeName }}">
                    <a href="{{ route("allianceindustry.orderDetails",$delivery->order_id) }}">{{ $delivery->order->type->typeName }}</a>
                </td>
            @endif
            <td data-order="{{ $delivery->quantity }}" data-filter="_">
                {{ $delivery->quantity }}
            </td>
            <td data-order="{{ $delivery->completed_at?carbon($delivery->completed_at)->timestamp:0 }}" data-filter="_">
                @include("allianceindustry::partials.boolean",["value"=>$delivery->completed])
                @if($delivery->completed_at)
                    @include("allianceindustry::partials.time",["date"=>$delivery->completed_at])
                @endif
            </td>
            @if($showOrder ?? false)
                <td data-order="{{ $delivery->order->unit_price }}" data-filter="_">
                    {{ number($delivery->order->unit_price) }} ISK
                </td>
            @endif
            <td data-order="{{ $delivery->order->unit_price * $delivery->quantity }}" data-filter="_">
                {{ number($delivery->order->unit_price * $delivery->quantity) }} ISK
            </td>
            <td data-order="{{ $delivery->accepted }}" data-filter="_">
                @include("allianceindustry::partials.time",["date"=>$delivery->accepted])
            </td>
            @if($showOrder ?? false)
                <td data-order="{{ $delivery->order->user->id ?? 0}}" data-filter="{{ $delivery->order->user->main_character->name ?? trans('web::seat.unknown')}}">
                    @include("web::partials.character",["character"=>$delivery->order->user->main_character ?? null])
                </td>
            @endif
            <td data-order="{{ $delivery->user->id ?? 0}}" data-filter="{{ $delivery->user->main_character->name ?? trans('web::seat.unknown')}}">
                @include("web::partials.character",["character"=>$delivery->user->main_character ?? null])
            </td>
            <td data-order="{{ $delivery->user->main_character->affiliation->corporation_id ?? 0}}" data-filter="{{ $delivery->user->main_character->affiliation->corporation->name ?? trans('web::seat.unknown')}}">
                @include('web::partials.corporation', ['corporation' => $delivery->user->main_character->affiliation->corporation ?? null])
            </td>
            <td data-order="{{ $delivery->user->main_character->affiliation->alliance_id }}" data-filter="{{ $delivery->user->main_character->affiliation->alliance->name ?? trans('web::seat.unknown') }}">
                @include('web::partials.alliance', ['alliance' => $delivery->user->main_character->affiliation->alliance ?? null])
            </td>
                <td data-order="{{ $delivery->order->location_id }}" data-filter="{{ $delivery->order->location()->name }}">
                    @include("allianceindustry::partials.longTextTooltip",["text"=>$delivery->order->location()->name])
                </td>
            <td class="d-flex flex-row">
                @can("allianceindustry.same-user",$delivery->user_id)
                    <form action="{{ route("allianceindustry.setDeliveryState",$delivery->order_id) }}" method="POST" style="width: 50%">
                        @csrf
                        <input type="hidden" name="delivery" value="{{ $delivery->id }}">

                        @if($delivery->completed)
                            <button type="submit" class="btn btn-warning text-nowrap confirmform btn-block"
                                    data-seat-action="mark this delivery as in progress">In Progress
                            </button>
                            <input type="hidden" name="completed" value="0">
                        @else
                            <button type="submit" class="btn btn-primary text-nowrap confirmform btn-block"
                                    data-seat-action="mark this delivery as delivered">Delivered
                            </button>
                            <input type="hidden" name="completed" value="1">
                        @endif
                    </form>

                    @if(!$delivery->completed || auth()->user()->can("allianceindustry.admin"))
                        <form action="{{ route("allianceindustry.deleteDelivery",$delivery->order_id) }}" method="POST" style="width: 50%">
                            @csrf
                            <input type="hidden" name="delivery" value="{{ $delivery->id }}">

                            <button type="submit" class="btn btn-danger text-nowrap confirmform ml-1 btn-block"
                                    data-seat-action="cancel this delivery">Cancel Delivery
                            </button>
                        </form>
                    @endif
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>