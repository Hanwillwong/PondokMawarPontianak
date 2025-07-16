@extends('layouts.main')
@section('container')
<main class="pt-90">
<div class="mb-4 pb-4"></div>
<section class="py-5">
    <div class="mx-auto" style="max-width: 600px;">
        <!-- ✅ Judul -->
        <div class="text-center mb-5">
            <h2 class="fw-semibold mb-2">Thank You for Your Order!</h2>
            <p class="text-muted mb-0">We've received your order and it's being processed.</p>
        </div>

        <!-- ✅ Informasi Ringkas Order -->
        <div class="border rounded-3 p-4 mb-4 shadow-sm bg-white">
            <h5 class="mb-3">Order Summary</h5>
            <div class="mb-2"><strong>Order Number:</strong> {{ $order->reference_number }}</div>
            <div class="mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</div>
            <div class="mb-2"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</div>
            <div class="mb-2"><strong>Shipping:</strong> {{ $order->purchase_type === 'delivery' ? 'Delivery to Address' : 'Pickup at Store' }}</div>
            <div><strong>Total:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</div>
        </div>

        <!-- ✅ Produk yang Dibeli -->
        <div class="border rounded-3 p-4 shadow-sm bg-white">
            <h5 class="mb-3">Order Items</h5>
            <table class="table table-borderless table-sm mb-0">
                <thead class="border-bottom">
                    <tr>
                        <th class="text-start">Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->order_detail as $detail)
                        <tr>
                            <td class="text-start">{{ $detail->product->name }}</td>
                            <td class="text-end">{{ $detail->quantity }}</td>
                            <td class="text-end">Rp{{ number_format($detail->price_at_order, 0, ',', '.') }}</td>
                            <td class="text-end">Rp{{ number_format($detail->price_at_order * $detail->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-top">
                    <tr class="fw-semibold">
                        <td colspan="3" class="text-end">Total</td>
                        <td class="text-end">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
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
@endpush
