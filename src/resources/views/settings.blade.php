@extends('web::layouts.grids.12')

@section('title', "About")
@section('page_header', "About")


@section('full')
    <div class="card">
        <div class="card-body">
            <h5 class="card-header">
                Settings
            </h5>
            <div class="card-text my-3 mx-3">
                <form action="{{ route("allianceindustry.saveSettings") }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="market">Market Hub</label>
                        <select name="market" id="market" class="form-control">
                            <option value="jita" @selected($marketHub==="jita")>Jita</option>
                            <option value="perimeter" @selected($marketHub==="perimeter")>Perimeter</option>
                            <option value="universe" @selected($marketHub==="universe")>Universe</option>
                            <option value="amarr" @selected($marketHub==="amarr")>Amarr</option>
                            <option value="dodixie" @selected($marketHub==="dodixie")>Dodixie</option>
                            <option value="hek" @selected($marketHub==="hek")>Hek</option>
                            <option value="rens" @selected($marketHub==="rens")>Rens</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pricetype" id="markettypesell" value="sell" @checked($priceType==="sell")>
                            <label class="form-check-label" for="markettypesell">
                                Sell Prices
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pricetype" id="markettypebuy" value="buy" @checked($priceType==="buy")>
                            <label class="form-check-label" for="markettypebuy">
                                Buy Prices
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mpp">Minimum Profit Percentage</label>
                        <input type="number" value="{{ $mpp }}" min="0" step="0.1" id="mpp" name="minimumprofitpercentage" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="pingRolesOrderCreation">Notifications: Roles to ping on order creation</label>
                        <input type="text" id="pingRolesOrderCreation" name="pingRolesOrderCreation" class="form-control" value="{{ $orderCreationPingRoles }}">
                        <small class="text-muted">Please copy&paste the discord role ids separated by a space</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop