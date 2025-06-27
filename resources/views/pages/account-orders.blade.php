@extends('layouts.main')

@push('styles')
<style>
    .nav-tabs {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        flex-wrap: nowrap;
    }

    .nav-tabs .nav-item {
        display: inline-block;
    }

    .nav-tabs .nav-link {
        color: #666;
        opacity: 0.6;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link.active {
        font-weight: bold;
        opacity: 1;
        color: #000;
        border-bottom: 2px solid #000;
        background-color: transparent;
    }

    .nav-tabs::-webkit-scrollbar {
        display: none;
    }

    .nav-tabs {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush


@section('container')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{route('pages.account')}}" class="menu-link menu-link_us-s">Dashboard</a></li>
            <li><a href="{{ route('account.orders') }}" class="menu-link menu-link_us-s">Orders</a></li>
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
          <div class="page-content my-account__dashboard">

            @if($orders->isEmpty())
                <p>Tidak ada pesanan.</p>
            @else
                {{-- TAB --}}
                <ul class="nav nav-tabs d-flex mb-4" id="orderTabs" role="tablist">
                  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#all">Semua</a></li>
                  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#unpaid">Belum Bayar</a></li>
                  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#processing">Sedang Diproses</a></li>
                  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#ready">Siap Di Ambil</a></li>
                  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#shipped">Dikirim</a></li>
                  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#completed">Selesai</a></li>
                  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#failed">Gagal</a></li>
              </ul>

                {{-- ISI TAB --}}
                <div class="tab-content">
                    {{-- SEMUA --}}
                    <div class="tab-pane fade show active" id="all">
                        @include('components.order-list', ['orders' => $orders])
                    </div>

                    {{-- BELUM BAYAR --}}
                    <div class="tab-pane fade" id="unpaid">
                        @include('components.order-list', ['orders' => $orders->where('status.label', 'pending')])
                    </div>

                    {{-- SEDANG DIKEMAS --}}
                    <div class="tab-pane fade" id="processing">
                        @include('components.order-list', ['orders' => $orders->where('status.label', 'ready to send')])
                    </div>

                    {{-- SIAP DIAMBIL --}}
                    <div class="tab-pane fade" id="ready">
                        @include('components.order-list', ['orders' => $orders->where('status.label', 'ready to pick up')])
                    </div>

                    {{-- DIKIRIM --}}
                    <div class="tab-pane fade" id="shipped">
                        @include('components.order-list', ['orders' => $orders->where('status.label', 'delivered')])
                    </div>

                    {{-- SELESAI --}}
                    <div class="tab-pane fade" id="completed">
                        @include('components.order-list', ['orders' => $orders->whereIn('status.label', 'completed')])
                    </div>

                    {{-- GAGAL --}}
                    <div class="tab-pane fade" id="failed">
                        @include('components.order-list', ['orders' => $orders->whereIn('status.label', 'failed')])
                    </div>
                </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>

@endsection