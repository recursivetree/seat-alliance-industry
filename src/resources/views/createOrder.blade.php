@extends('web::layouts.grids.12')

@section('title', "Create Order")
@section('page_header', "Create Order")


@section('full')
    <div class="card">
        <div class="card-body">
            <h5 class="card-header d-flex flex-row align-items-baseline">
                Create Order
                <a href="{{ route("allianceindustry.orders") }}" class="btn btn-danger ml-auto">Cancel</a>
            </h5>
            <p class="card-text">

                <form action="{{ route("allianceindustry.submitOrder") }}" method="POST" id="orderForm">
                    @csrf

                    <div class="form-group">
                        <label for="itemsTextarea">Items</label>
                        <textarea
                            id="itemsTextarea"
                            name="items"
                            class="form-control"
                            placeholder="{{"Tristan 100\nOmen 100\nTritanium 30000"}}"
                            rows="10"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="profit">Reward %</label>
                        <input type="number" id="profit" class="form-control" value="{{ $mpp }}" min="{{ $mpp }}" step="0.1" name="profit">
                        <small class="text-muted">The minimal profit is {{$mpp}}%</small>

                        <div class="form-check">
                            <input type="checkbox" id="addProfitToManualPrices" class="form-check-input" checked name="addProfitToManualPrices">
                            <label for="addProfitToManualPrices" class="form-check-label">Add Reward to Manual Prices</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="days">Days to complete</label>
                        <input type="number" id="days" class="form-control" name="days" min="1" step="1" value="30">
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <select id="location" class="form-control" name="location">
                            @foreach($stations as $station)
                                <option value="{{ $station->station_id }}">{{ $station->name }}</option>
                            @endforeach
                            @foreach($structures as $structure)
                                <option value="{{ $structure->structure_id }}">{{ $structure->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Order</button>
                </form>

            </p>
        </div>
    </div>
@stop

@push("javascript")
    <script>
        $(document).ready( function () {
            $("#location").select2()
            $('[data-toggle="tooltip"]').tooltip()
            $('.data-table').DataTable();
        });
    </script>
@endpush