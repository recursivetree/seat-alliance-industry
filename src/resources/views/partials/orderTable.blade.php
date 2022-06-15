<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th>Order</th>
        <th>Quantity</th>
        <th>Completed</th>
        <th>Unit Price</th>
        <th>Total Price</th>
        <th>Character</th>
        <th>Corporation</th>
        <th>Alliance</th>
        <th>Location</th>
        <th>Created</th>
        <th>Until</th>
    </tr>
    </thead>
    <tbody>
    @if($orders->isEmpty())
        <tr>
            <td colspan="11">There are no order to display</td>
        </tr>
    @endif
    @foreach($orders as $order)
        <tr>
            <td>
                <a href="{{ route("allianceindustry.orderDetails",$order->id) }}">{{ $order->type->typeName }}</a>
            </td>
            <td>
                {{$order->assignedQuantity()}}/{{ $order->quantity }}
            </td>
            <td>
                @include("allianceindustry::partials.boolean",["value"=>$order->completed])
                @if($order->completed_at)
                    @include("allianceindustry::partials.time",["date"=>$order->completed_at])
                @endif
            </td>
            <td>
                {{ number($order->unit_price) }} ISK
            </td>
            <td>
                {{ number($order->unit_price * $order->quantity) }} ISK
            </td>
            <td>
                @include("web::partials.character",["character"=>$order->user->main_character])
            </td>
            <td>
                @include('web::partials.corporation', ['corporation' => $order->user->main_character->affiliation->corporation])
            </td>
            <td>
                @include('web::partials.alliance', ['alliance' => $order->user->main_character->affiliation->alliance])
            </td>
            <td>
                {{ $order->location()->name }}
            </td>
            <td>
                @include("allianceindustry::partials.time",["date"=>$order->created_at])
            </td>
            <td>
                @include("allianceindustry::partials.time",["date"=>$order->produce_until])
            </td>
        </tr>
    @endforeach
    </tbody>
</table>