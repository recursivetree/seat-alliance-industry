@extends('web::layouts.app')

@section('title', 'Build Time Price Provider')
@section('page_header', 'Build Time Price Provider')
@section('page_description', 'Build Time Price Provider')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Price Provider</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('allianceindustry.priceprovider.buildtime.configuration.post') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $id ?? "" }}">

                <div class="form-group">
                    <label for="name">{{ trans('pricescore::settings.name') }}</label>
                    <input required type="text" name="name" id="name" class="form-control" placeholder="{{ trans('pricescore::settings.name_placeholder') }}" value="{{ $name ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="manufacturing">Manufacturing Time Modifier</label>
                    <input type="number" name="manufacturing" id="manufacturing" class="form-control" min="0" value="{{ $manufacturing_multiplier }}">
                </div>

                <div class="form-group">
                    <label for="reactions">Reaction Time Modifier</label>
                    <input type="number" name="reactions" id="reactions" class="form-control" min="0" value="{{ $reaction_multiplier }}">
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('pricescore::priceprovider.save')  }}</button>
            </form>
        </div>
    </div>

@endsection
