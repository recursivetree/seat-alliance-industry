<table class="table table-striped table-hover">
    <thead>
    <tr>
        @if($showOrder ?? false)
            <th>Order</th>
        @endif
        <th>Amount</th>
        <th>Completed</th>
        <th>Accepted</th>
        <th>Character</th>
        <th>Corporation</th>
        <th>Alliance</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @if($deliveries->isEmpty())
        <tr>
            <td colspan="7">
                No suppliers signed up for deliveries yet
            </td>
        </tr>
    @endif
    @foreach($deliveries as $delivery)
        <tr>
            @if($showOrder ?? false)
                <td>
                    <a href="{{ route("allianceindustry.orderDetails",$delivery->order_id) }}">{{ $delivery->order->type->typeName }}</a>
                </td>
            @endif
            <td>{{ $delivery->quantity }}</td>
            <td>
                @include("allianceindustry::partials.boolean",["value"=>$delivery->completed])
                @if($delivery->completed_at)
                    @include("allianceindustry::partials.time",["date"=>$delivery->completed_at])
                @endif
            </td>
            <td>
                @include("allianceindustry::partials.time",["date"=>$delivery->accepted])
            </td>
            <td>
                @include("web::partials.character",["character"=>$delivery->user->main_character])
            </td>
            <td>
                @include('web::partials.corporation', ['corporation' => $delivery->user->main_character->affiliation->corporation])
            </td>
            <td>
                @include('web::partials.alliance', ['alliance' => $delivery->user->main_character->affiliation->alliance])
            </td>
            <td class="d-flex flex-row justify-content-between">
                @can("allianceindustry.same-user",$delivery->user_id)
                    <form action="{{ route("allianceindustry.setDeliveryState",$delivery->order_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="delivery" value="{{ $delivery->id }}">

                        @if($delivery->completed)
                            <button type="submit" class="btn btn-warning text-nowrap confirmform" data-seat-action="mark this delivery as in progress">Mark as in Progress</button>
                            <input type="hidden" name="completed" value="0">
                        @else
                            <button type="submit" class="btn btn-warning text-nowrap confirmform" data-seat-action="mark this delivery as delivered">Mark Delivered</button>
                            <input type="hidden" name="completed" value="1">
                        @endif
                    </form>

                    <form action="{{ route("allianceindustry.deleteDelivery",$delivery->order_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="delivery" value="{{ $delivery->id }}">

                        <button type="submit" class="btn btn-danger text-nowrap confirmform ml-1" data-seat-action="cancel this delivery">Cancel Delivery</button>
                    </form>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>