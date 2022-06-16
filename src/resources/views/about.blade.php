@extends('web::layouts.grids.12')

@section('title', "About")
@section('page_header', "About")


@section('full')
    <div class="card">
        <div class="card-body">
            <h5 class="card-header">
                About
            </h5>
            <div class="card-text my-3 mx-3">
                <h6>
                    What is seat-alliance-industry?
                </h6>
                <p>
                    This module is intended to help a corporation, alliance or other organisations to set up a industry order market place.
                    It is a platform to release a order for a item, let people know you published this order and keep track who is producing it for you.
                </p>

                <h6>Development</h6>
                <p>
                    Thanks for installing seat-alliance-industry.
                    I hope you enjoy working with seat-alliance-industry.
                    To support the development, have you considered donating something?
                    Donations are always welcome and motivate me to put more effort into this project, although they are by no means required. If you end up using this module a lot, I'd appreciate a donation.
                    You can give ISK, PLEX or Ships to 'recursivetree'.
                </p>
                <p>
                    If you have ideas, features or bugs you want to report, feel free to contact me on discord (<a href="https://eveseat.github.io/docs/about/contact/">SeAT discord server</a> or recursive_tree#6692) or open an issue on GitHub (<a href="https://github.com/recursivetree/seat-alliance-industry">recursivetree/seat-alliance-industry</a>).
                </p>

                <h6>Usage</h6>
                <p>
                    There are two basic concepts: orders and deliveries.
                    A order is pretty self-explanatory: You order an amount of an item for a price and wait for others to complete you order.
                    A delivery is the promise that you will complete a order, but you don't have the item yet (because they are still being manufactured).
                    Once you have the items of a delivery you signed up for, contract the items to the requester of the order and mark your order as completed.
                    As soon as every delivery of an order is marked as completed, the order is marked as completed and can be deleted.
                </p>
            </div>
        </div>
    </div>
@stop