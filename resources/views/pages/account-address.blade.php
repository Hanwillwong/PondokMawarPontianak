@extends('layouts.main')
@section('container')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{ route('account.orders') }}" class="menu-link menu-link_us-s">Orders</a></li>
            <li><a href="{{route('pages.account-address')}}" class="menu-link menu-link_us-s">Addresses</a></li>
            <li>
                <form method="POST" action="{{route('logout')}}" id="logout-form">
                    @csrf
                    <a href="{{route('logout')}}" class="" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                </form>
            </li>
          </ul>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__address">
            <div class="row">
              <div class="col-6">
                <p class="notice">The following addresses will be used on the checkout page by default.</p>
              </div>
              <div class="col-6 text-right">
                <a href="{{route('pages.account-address.add')}}" class="btn btn-sm btn-info">Add New</a>
              </div>
            </div>
            <div class="my-account__address-list row">
              <h5>Shipping Address</h5>

              @foreach ($addresses as $address)
                <div class="my-account__address-item col-md-6">
                    <div class="my-account__address-item__title">
                        <h5>{{ $address->name }}</h5>
                        <a href="{{route('pages.account-address.edit', $address->id)}}">Edit</a>
                    </div>
                    <div class="my-account__address-item__detail">
                        <p>{{ $address->address }}</p>
                        <p>{{ $address->city }}, {{ $address->province }}</p>
                        <p>Kode Pos: {{ $address->post_code }}</p>
                        <br>
                        <p>Phone : {{ $address->phone }}</p>
                    </div>
                </div>
                <hr>
              @endforeach

              
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

@endsection