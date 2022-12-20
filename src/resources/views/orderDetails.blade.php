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
                    @if($order->deliveries->isEmpty() || $order->completed)
                        <form action="{{ route("allianceindustry.deleteOrder") }}" method="POST">
                            @csrf
                            <input type="hidden" name="order" value="{{ $order->id }}">
                            <button type="submit" class="btn btn-danger">Close this Order</button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>

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
@stop

@push("javascript")
    <script>
        $(document).ready( function () {
            $('[data-toggle="tooltip"]').tooltip()
            $('.data-table').DataTable({
                stateSave: true
            });
        });
    </script>
@endpush