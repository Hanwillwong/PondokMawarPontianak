@extends('layouts.main')
@section('container')
<main class="pt-90">
<div class="mb-4 pb-4"></div>
<section class="shop-checkout container">
    <h2 class="page-title">Shipping and Checkout</h2>

    <div class="checkout-steps">
        <a href="{{ route('cart') }}" class="checkout-steps__item active">
            <span class="checkout-steps__item-number">01</span>
            <span class="checkout-steps__item-title">
                <span>Shopping Bag</span>
                <em>Manage Your Items List</em>
            </span>
        </a>
        <a href="{{ route('checkout') }}" class="checkout-steps__item active">
            <span class="checkout-steps__item-number">02</span>
            <span class="checkout-steps__item-title">
                <span>Shipping and Checkout</span>
                <em>Checkout Your Items List</em>
            </span>
        </a>
        <a href="#" class="checkout-steps__item">
            <span class="checkout-steps__item-number">03</span>
            <span class="checkout-steps__item-title">
                <span>Confirmation</span>
                <em>Review And Submit Your Order</em>
            </span>
        </a>
    </div>

    <form name="checkout-form" action="" method="POST">
        @csrf
        <div class="checkout-form">
            <div class="billing-info__wrapper">
                <h4 class="mb-3">Select Purchase Type</h4>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="shipping_method" id="shipping_pickup" value="pickup" checked>
                    <label class="form-check-label" for="shipping_pickup">Pickup at Store</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="shipping_method" id="shipping_delivery" value="delivery">
                    <label class="form-check-label" for="shipping_delivery">Delivery to Address</label>
                </div>

                <div id="delivery-address-form">
                    <!-- <h4>Shipping Details</h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="name" value="">
                                <label for="name">Full Name *</label>
                                <span class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="phone" value="">
                                <label for="phone">Phone Number *</label>
                                <span class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="province" value="">
                                <label for="province">Province *</label>
                                <span class="text-danger"></span>
                            </div>
                        </div>                        
                        <div class="col-md-4">
                            <div class="form-floating mt-3 mb-3">
                                <input type="text" class="form-control" name="city" value="">
                                <label for="city">City *</label>
                                <span class="text-danger"></span>
                            </div>                            
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="post_code" value="">
                                <label for="post_code">Kode Pos *</label>
                                <span class="text-danger"></span>
                            </div>
                        </div> 
                        <div class="col-md-12">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="address" value="">
                                <label for="address">address *</label>
                                <span class="text-danger"></span>
                            </div>
                        </div>
                    </div> -->

                    <div class="my-account__address-item__title d-flex justify-content-between align-items-center">
                        <h5 class="mb-3">Shipping Address</h5>
                        <a class="mb-3" href="{{route('pages.account-address.add')}}">Add</a>
                    </div>
                    
                    <div class="my-account__address-list row">
                        @foreach ($addresses as $address)
                            <div class="my-account__address-item col-md-6 px-3 mb-1">
                                <div class="border rounded p-3 h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_id" id="address-{{ $address->id }}" value="{{ $address->id }}" required>
                                        <label class="form-check-label w-100" for="address-{{ $address->id }}">
                                            <div class="my-account__address-item__title d-flex justify-content-between align-items-center">
                                                <h5 class="mb-1">{{ $address->name }}</h5>
                                                <a href="{{route('pages.account-address.edit', $address->id)}}">Edit</a>
                                            </div>
                                            <div class="my-account__address-item__detail">
                                                <p>{{ $address->address }}</p>
                                                <p>{{ $address->city }}, {{ $address->province }}</p>
                                                <p>Kode Pos: {{ $address->post_code }}</p>
                                                <p>Mobile: {{ $address->phone }}</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>
            </div>
            <div class="shopping-cart__totals-wrapper">
                <div class="sticky-content">
                    <div class="shopping-cart__totals">
                        <h3>Your Order</h3>
                        <table class="checkout-cart-items">
                            <thead>
                                <tr><th>PRODUCT</th><th align="right">SUBTOTAL</th></tr>
                            </thead>
                            <tbody>
                                @foreach($cartDetails as $item)
                                    <tr>
                                        <td>{{ $item['product']->name }} x {{ $item['quantity'] }}</td>
                                        <td align="right">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="checkout-totals">
                            <tbody>
                                <tr>
                                    <th>Total</th>
                                    <td id="cart-total">Rp{{ number_format($total, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="checkout__payment-methods">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay1" value="bank" checked>
                            <label class="form-check-label" for="pay1">Direct bank transfer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay2" value="cod">
                            <label class="form-check-label" for="pay2">Cash on delivery</label>
                        </div>
                    </div>

                    <button type="button" id="pay-button" class="btn btn-primary btn-checkout mt-3">PLACE ORDER</button>
                    <input type="hidden" id="snap_token" name="snap_token">
                </div>
            </div>
        </div>
    </form>
</section>
</main>
@endsection

@push("scripts")
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pickupRadio = document.getElementById('shipping_pickup');
        const deliveryRadio = document.getElementById('shipping_delivery');
        const addressForm = document.getElementById('delivery-address-form');

        function toggleAddressForm() {
            if (pickupRadio.checked) {
                addressForm.style.display = 'none';
                addressForm.querySelectorAll('input').forEach(input => {
                    input.dataset.required = input.required;
                    input.required = false;
                });
            } else {
                addressForm.style.display = 'block';
                addressForm.querySelectorAll('input').forEach(input => {
                    input.required = input.dataset.required === "true" || input.dataset.required === "1" || input.dataset.required === "required";
                });
            }
        }

        pickupRadio.addEventListener('change', toggleAddressForm);
        deliveryRadio.addEventListener('change', toggleAddressForm);

        toggleAddressForm(); // initial call
    });
</script>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        fetch('{{ route("midtrans.token") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            window.snap.pay(data.snapToken, {
                onSuccess: function(result){
                    alert('Pembayaran berhasil');
                    console.log(result);
                    // Arahkan ke halaman sukses
                    window.location.href = '/checkout/success?order_id=' + data.order_id;
                },
                onPending: function(result){
                    alert('Menunggu pembayaran');
                    console.log(result);
                },
                onError: function(result){
                    alert('Pembayaran gagal');
                    console.log(result);
                },
                onClose: function(){
                    alert('Kamu menutup popup pembayaran');
                }
            });
        });
    });
</script>
@endpush
