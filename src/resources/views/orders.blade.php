@extends('web::layouts.grids.12')

@section('title', "About")
@section('page_header', "About")


@section('full')
    <div class="card">
        <div class="card-body">
            <h5 class="card-header d-flex flex-row align-items-baseline">
                Open Orders
                @can("allianceindustry.create_orders")
                    <a href="{{ route("allianceindustry.createOrder") }}" class="btn btn-primary ml-auto">Create Order</a>
                @endcan
            </h5>
            <div class="card-text pt-3">
                @include("allianceindustry::partials.orderTable",["orders"=>$orders])
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-header d-flex flex-row align-items-baseline">
                Your Orders
            </h5>
            <div class="card-text pt-3">
                @include("allianceindustry::partials.orderTable",["orders"=>$personalOrders])
            </div>
        </div>
    </div>
@stop

@push("javascript")
    <script>
        $(document).ready( function () {
            $('[data-toggle="tooltip"]').tooltip()
            $('.data-table').DataTable();
        });
    </script>
@endpush