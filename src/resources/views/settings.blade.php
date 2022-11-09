@extends('web::layouts.grids.12')

@section('title', "Settings")
@section('page_header', "Settings")


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
                        <small class="text-muted">To incentive production, the plugin applies this % of the item value on top of the price. While creating an order, you can always choose to give a higher profit, but to avoid players ripping off others, they can't go below this value.</small>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="allowPriceBelowAutomatic" class="form-check-input" name="allowPriceBelowAutomatic" @checked($allowPriceBelowAutomatic)>
                            <label for="allowPriceBelowAutomatic" class="form-check-label">Allow Manual Prices below automatic Prices</label>
                        </div>
                        <small class="text-muted">To avoid scam orders, manual prices are ignored if they are for less than the automatic price.</small>
                    </div>

                    <div class="form-group">
                        <label for="pingRolesOrderCreation">Notifications: Roles to ping on order creation</label>
                        <input type="text" id="pingRolesOrderCreation" name="pingRolesOrderCreation" class="form-control" value="{{ $orderCreationPingRoles }}">
                        <small class="text-muted">Please copy&paste the discord role ids separated by a space. If you enable developer mode in your settings, you can get the IDs by clicking the role.</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop