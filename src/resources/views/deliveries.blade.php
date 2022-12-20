@extends('web::layouts.grids.12')

@section('title', "Your Deliveries")
@section('page_header', "Your Deliveries")


@section('full')
    <div class="card">
        <div class="card-body">
            <h5 class="card-header d-flex flex-row align-items-baseline">
                Your Deliveries
            </h5>
            <div class="card-text pt-3">
                @include("allianceindustry::partials.deliveryTable",["deliveries"=>$deliveries,"showOrder"=>true])
            </div>
        </div>
    </div>
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