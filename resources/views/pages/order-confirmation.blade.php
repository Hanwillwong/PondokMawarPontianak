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

    <div class="order-complete">
        <div class="order-complete__message">
          <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="40" fill="#B9A16B" />
            <path
              d="M52.9743 35.7612C52.9743 35.3426 52.8069 34.9241 52.5056 34.6228L50.2288 32.346C49.9275 32.0446 49.5089 31.8772 49.0904 31.8772C48.6719 31.8772 48.2533 32.0446 47.952 32.346L36.9699 43.3449L32.048 38.4062C31.7467 38.1049 31.3281 37.9375 30.9096 37.9375C30.4911 37.9375 30.0725 38.1049 29.7712 38.4062L27.4944 40.683C27.1931 40.9844 27.0257 41.4029 27.0257 41.8214C27.0257 42.24 27.1931 42.6585 27.4944 42.9598L33.5547 49.0201L35.8315 51.2969C36.1328 51.5982 36.5513 51.7656 36.9699 51.7656C37.3884 51.7656 37.8069 51.5982 38.1083 51.2969L40.385 49.0201L52.5056 36.8996C52.8069 36.5982 52.9743 36.1797 52.9743 35.7612Z"
              fill="white" />
          </svg>
          <h3>Your order is completed!</h3>
          <p>Thank you. Your order has been received.</p>
        </div>
        <div class="order-info">
          <div class="order-info__item">
            <label>Order Number</label>
            <span>13119</span>
          </div>
          <div class="order-info__item">
            <label>Date</label>
            <span>27/10/2023</span>
          </div>
          <div class="order-info__item">
            <label>Total</label>
            <span>$81.40</span>
          </div>
          <div class="order-info__item">
            <label>Paymetn Method</label>
            <span>Direct Bank Transfer</span>
          </div>
        </div>
        <div class="checkout__totals-wrapper">
          <div class="checkout__totals">
            <h3>Order Details</h3>
            <table class="checkout-cart-items">
              <thead>
                <tr>
                  <th>PRODUCT</th>
                  <th>SUBTOTAL</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    Zessi Dresses x 2
                  </td>
                  <td>
                    $32.50
                  </td>
                </tr>
                <tr>
                  <td>
                    Kirby T-Shirt
                  </td>
                  <td>
                    $29.90
                  </td>
                </tr>
              </tbody>
            </table>
            <table class="checkout-totals">
              <tbody>
                <tr>
                  <th>SUBTOTAL</th>
                  <td>$62.40</td>
                </tr>
                <tr>
                  <th>SHIPPING</th>
                  <td>Free shipping</td>
                </tr>
                <tr>
                  <th>VAT</th>
                  <td>$19</td>
                </tr>
                <tr>
                  <th>TOTAL</th>
                  <td>$81.40</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
</section>
</main>
@endsection

@push("scripts")
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const pickupRadio = document.getElementById('shipping_pickup');
    const deliveryRadio = document.getElementById('shipping_delivery');
    const addressForm = document.getElementById('delivery-address-form');
    const codOption = document.getElementById('cod-payment-option');

    function toggleAddressForm() {
        if (pickupRadio.checked) {
            addressForm.style.display = 'none';

            // Atur required ke false untuk semua input dalam form alamat
            addressForm.querySelectorAll('input').forEach(input => {
                input.dataset.required = input.required;
                input.required = false;
            });

            // ✅ COD hanya tersedia untuk pickup
            codOption.style.display = 'block';

        } else {
            addressForm.style.display = 'block';

            // Atur input required kembali sesuai data-required
            addressForm.querySelectorAll('input').forEach(input => {
                input.required = input.dataset.required === "true" || input.dataset.required === "1" || input.dataset.required === "required";
            });

            // ❌ Sembunyikan COD saat delivery
            codOption.style.display = 'none';

            // Otomatis ubah pilihan pembayaran jadi Midtrans jika sebelumnya COD
            const codInput = codOption.querySelector('input');
            if (codInput.checked) {
                document.getElementById('midtrans').checked = true;
            }
        }
    }

    pickupRadio.addEventListener('change', toggleAddressForm);
    deliveryRadio.addEventListener('change', toggleAddressForm);

    toggleAddressForm(); // Initial call
});

</script>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<!-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script> -->

<script>
    document.getElementById('pay-button').addEventListener('click', function () {
    const shippingMethod = document.querySelector('input[name="shipping_method"]:checked')?.value;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;

    if (shippingMethod === 'delivery') {
        const selectedAddress = document.querySelector('input[name="address_id"]:checked');
        if (!selectedAddress) {
            alert("Silakan pilih alamat pengiriman terlebih dahulu.");
            return;
        }

        const addressId = selectedAddress.value;

        if (paymentMethod === 'midtrans') {
            // Proses Midtrans
            fetch('{{ route("midtrans.token") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    address_id: addressId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.snapToken) {
                    alert('Gagal mendapatkan Snap Token');
                    return;
                }

                window.snap.pay(data.snapToken, {
                    display: 'popup',
                    onSuccess: function(result){
                        alert('Pembayaran berhasil');
                        console.log(result);
                        window.location.href = '/checkout/success?order_id=' + data.order_id;
                    },
                    onPending: function(result){
                        alert('Menunggu pembayaran');
                        console.log(result);
                    },
                    onError: function(result){
                        alert('Terjadi kesalahan saat pembayaran');
                        console.log(result);
                    },
                    onClose: function(){
                        alert('Kamu menutup popup pembayaran tanpa menyelesaikan');
                    }
                });
            })
            .catch(error => {
                console.error('Terjadi error saat memproses pembayaran:', error);
                alert('Gagal memproses pembayaran');
            });
        } else {
            // Jika COD saat delivery, harusnya tidak bisa — bisa kasih alert
            alert('COD tidak tersedia untuk pengiriman ke alamat.');
        }

    } else {
        // Pickup: langsung submit form tanpa validasi alamat
        document.querySelector('form[name="checkout-form"]').submit();
    }
});

</script>
@endpush
