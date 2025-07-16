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
            <a class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Review And Submit Your Order</em>
                </span>
            </a>
        </div>

        <form name="checkout-form" action="{{ route('checkout.store') }}" method="POST">
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
                        <div class="my-account__address-item__title d-flex justify-content-between align-items-center">
                            <h5 class="mb-3">Shipping Address</h5>
                            <a class="mb-3" href="{{route('pages.account-address.add', ['redirect' => url()->current()]) }}">Add</a>
                        </div>

                        <div class="my-account__address-list row">
                            @foreach ($addresses as $address)
                            <div class="my-account__address-item col-md-6 px-3 mb-1">
                                <div class="border rounded p-3 h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_id" value="{{ $address->id }}" id="address-{{ $address->id }}">
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
                            <table class="cart-totals">
                                <tbody>
                                    <tr class="cart-total">
                                        <th>Total</th>
                                        <td id="cart-total">Rp{{ number_format($total, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="checkout__payment-methods">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="midtrans" value="midtrans">
                                <label class="form-check-label" for="midtrans">Transfer Bank / QRIS / e-Wallet</label>
                            </div>
                            <div class="form-check" id="cod-payment-option">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">Cash on delivery</label>
                            </div>
                        </div>

                        <button type="button" id="pay-button" class="btn btn-primary btn-checkout mt-3">CHECKOUT</button>
                        <input type="hidden" id="snap_token" name="snap_token">
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pickupRadio = document.getElementById('shipping_pickup');
        const deliveryRadio = document.getElementById('shipping_delivery');
        const addressForm = document.getElementById('delivery-address-form');
        const codOption = document.getElementById('cod-payment-option');

        function toggleAddressForm() {
            if (pickupRadio.checked) {
                addressForm.style.display = 'none';
                addressForm.querySelectorAll('input').forEach(input => {
                    input.dataset.required = input.required;
                    input.required = false;
                });
                codOption.style.display = 'block';
            } else {
                addressForm.style.display = 'block';
                addressForm.querySelectorAll('input').forEach(input => {
                    input.required = input.dataset.required === "true" || input.dataset.required === "1" || input.dataset.required === "required";
                });
                codOption.style.display = 'none';

                const codInput = codOption.querySelector('input');
                if (codInput.checked) {
                    document.getElementById('midtrans').checked = true;
                }
            }
        }

        pickupRadio.addEventListener('change', toggleAddressForm);
        deliveryRadio.addEventListener('change', toggleAddressForm);
        toggleAddressForm(); // Initial
    });
</script>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', async function () {
        const shippingMethod = document.querySelector('input[name="shipping_method"]:checked')?.value;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;

        let addressId = null;
        if (shippingMethod === 'delivery') {
            const selectedAddress = document.querySelector('input[name="address_id"]:checked');
            if (!selectedAddress) {
                alert("Silakan pilih alamat pengiriman terlebih dahulu.");
                return;
            }
            addressId = selectedAddress.value;
        }

        // 🧠 Tahap 1: Simpan Order ke DB
        const responseOrder = await fetch('{{ route("checkout.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                shipping_method: shippingMethod,
                address_id: addressId,
                payment_method: paymentMethod
            })
        });

        const text = await responseOrder.text();
        console.log(text); // lihat output dulu
        const orderData = JSON.parse(text);

        if (!orderData.order_ref) {
            alert("Gagal menyimpan pesanan.");
            return;
        }

        if (paymentMethod !== 'midtrans') {
            // Jika COD, langsung redirect ke success
            window.location.href = '{{ route("order.confirmation") }}?order_ref=' + orderData.order_ref;
            return;
        }

        // 🧠 Tahap 2: Ambil Snap Token
        const responseToken = await fetch('{{ route("midtrans.token") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_ref: orderData.order_ref
            })
        });

        const tokenData = await responseToken.json();

        if (!tokenData.snapToken) {
            alert('Gagal mendapatkan Snap Token');
            return;
        }

        // 🧠 Jalankan Snap
        window.snap.pay(tokenData.snapToken, {
            onSuccess: function(result){
                window.location.href = '{{ route("order.confirmation") }}?order_ref=' + orderData.order_ref;
            },
            onPending: function(result){
                window.location.href = '/account-orders/';
            },
            onError: function(result){
                alert('Pembayaran gagal');
            },
            onClose: function(){
                // User tutup popup sebelum bayar
                fetch("/payment-cancelled", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        order_ref: orderData.order_ref
                    })
                }).then(res => res.json())
                .then(data => {
                    console.log(data.message);
                }).catch(err => {
                    console.error("Gagal rollback:", err);
                });
            }
        });
    });

</script>

@endpush
