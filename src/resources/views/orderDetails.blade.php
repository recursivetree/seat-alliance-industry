@extends('web::layouts.grids.12')

@section('title', "Order")
@section('page_header', "Order")


@section('full')
    <div class="card">
        <div class="card-body">
            <h5 class="card-header d-flex flex-row align-items-center px-1">
                Orders
                <a href="{{ route("allianceindustry.orders") }}" class="btn btn-primary ml-auto">Back</a>
            </h5>
            <div class="card-text pt-3">
                @include("allianceindustry::partials.orderTable",["orders"=>collect([$order])])

                @can("allianceindustry.same-user",$order->user_id)
                    <div class="d-flex flex-row">
                        @if($order->deliveries->isEmpty() || !$order->hasPendingDeliveries() || $order->completed || auth()->user()->can("allianceindustry.admin"))
                            <form action="{{ route("allianceindustry.deleteOrder") }}" method="POST" class="mx-1">
                                @csrf
                                <input type="hidden" name="order" value="{{ $order->id }}">
                                <button type="submit" class="btn btn-danger">Close this Order</button>
                            </form>
                        @endif

                        @if(!$order->completed && !$order->is_repeating)
                            <form action="{{ route("allianceindustry.updateOrderPrice") }}" method="POST" class="mx-1">
                                @csrf
                                <input type="hidden" name="order" value="{{ $order->id }}">
                                <button type="submit" class="btn btn-secondary confirmform"
                                        data-seat-action="update the price? Manual prices will be overwritten!">Update
                                    Price
                                </button>
                            </form>
                        @endif

                        @if(!$order->is_repeating)
                            <form action="{{ route("allianceindustry.extendOrderPrice") }}" method="POST" class="mx-1">
                                @csrf
                                <input type="hidden" name="order" value="{{ $order->id }}">
                                <button type="submit" class="btn btn-secondary confirmform"
                                        data-seat-action=" want to expand the time to deliver by 1 week">Extend Time
                                </button>
                            </form>
                        @endif
                    </div>

                @endcan
            </div>
        </div>
    </div>

    @if($order->items->count() > 1)
        <div class="card">
            <div class="card-body">
                <h5 class="card-header px-1">
                    Items
                </h5>
                <div class="card-text pt-3">
                    <ul class="list-group">
                        @foreach($order->items as $item)
                            <li class="list-group-item">
                                @include("web::partials.type",[
                                    'type_id'   => $item->type_id,
                                    'type_name' => $item->quantity."x ".$item->type->typeName,
                                    'variation' => $item->type->group->categoryID == 9 ? 'bpc' : 'icon',
                                ])
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if($order->is_repeating)
        <div class="card">
            <div class="card-body">
                <h5 class="card-header px-1">
                    Repeating Order
                </h5>
                <div class="card-text pt-3">
                    This is a order repeating itself every {{ number($order->repeat_interval,0) }} days. The next
                    repetition will be published on the {{ $order->repeat_date }}.
                </div>
            </div>
        </div>
    @endif

    @if(!$order->is_repeating)
        <div class="card">
            <div class="card-body">
                <h5 class="card-header px-1">
                    Deliveries
                </h5>
                <div class="card-text pt-3">
                    @include("allianceindustry::partials.deliveryTable",["deliveries"=>$order->deliveries])
                </div>
            </div>
        </div>

        @can("allianceindustry.create_deliveries")
            @if($order->assignedQuantity()<$order->quantity)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-header px-1">
                            Supply Item
                        </h5>
                        <div class="card-text my-3">

                            <form action="{{ route("allianceindustry.addDelivery",$order->id) }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number"
                                           min="1"
                                           max="{{ $order->quantity - $order->assignedQuantity() }}"
                                           step="1"
                                           value="{{ $order->quantity - $order->assignedQuantity() }}"
                                           class="form-control"
                                           id="quantity"
                                           name="quantity">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Supply this Item</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endcan
    @endif
@stop

@push("javascript")
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip()
            $('.data-table').DataTable({
                stateSave: true
            });
        });
    </script>
@endpush