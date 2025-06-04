@extends('layouts.main')
@section('container')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{route('pages.account')}}" class="menu-link menu-link_us-s">Dashboard</a></li>
            <li><a href="account-orders.html" class="menu-link menu-link_us-s">Orders</a></li>
            <li><a href="{{route('pages.account-address')}}" class="menu-link menu-link_us-s">Addresses</a></li>
            <li><a href="account-details.html" class="menu-link menu-link_us-s">Account Details</a></li>
            <li><a href="account-wishlist.html" class="menu-link menu-link_us-s">Wishlist</a></li>
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
                      <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger">Back</a>
                  </div>
              </div>

              <div class="row">
                  <div class="col-md-12">
                      <div class="card mb-5">
                        <div class="card-header">
                            <h5>Add New Address</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pages.account-address.update', $address->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $address->name) }}">
                                    <label for="name">Full Name *</label>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $address->phone) }}">
                                    <label for="phone">Phone Number *</label>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="province" value="{{ old('province', $address->province) }}">
                                    <label for="province">Province *</label>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="city" value="{{ old('city', $address->city) }}">
                                    <label for="city">City *</label>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="post_code" value="{{ old('post_code', $address->post_code) }}">
                                    <label for="post_code">Kode Pos *</label>
                                </div>
                                </div>
                                <div class="col-md-12">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
                                    <label for="address">Address *</label>
                                </div>
                                </div>
                                <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </div>
                            </form> 
                        </div>
                      </div>
                  </div>
              </div>
              <hr>                    
          </div>
        </div>
      </div>
    </section>
  </main>

@endsection