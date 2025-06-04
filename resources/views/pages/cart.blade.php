@extends('layouts.main')
@section('container')
<style>
    .cart-totals td {
        text-align: right;
    }
    .cart-total th, .cart-total td {
        color: green;
        font-weight: bold;
        font-size: 21px !important;
    }
</style>
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Cart</h2>
        <div class="checkout-steps">
            <a href="{{ route('cart') }}" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">01</span>
                <span class="checkout-steps__item-title">
                    <span>Shopping Bag</span>
                    <em>Manage Your Items List</em>
                </span>
            </a>
            <a href="{{ route('checkout') }}" class="checkout-steps__item">
                <span class="checkout-steps__item-number">02</span>
                <span class="checkout-steps__item-title">
                    <span>Shipping and Checkout</span>
                    <em>Checkout Your Items List</em>
                </span>
            </a>
            <a href="javascript:void();" class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Order Confirmation</em>
                </span>
            </a>
        </div>
        <div class="shopping-cart">
        @if(!empty(session('cart')))
            <div class="cart-table__wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th></th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $total = 0; @endphp
                    @foreach($cartDetails as $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;

                        $qtyInCart = $item['quantity']; // dari session cart
                        $remainingStock = $item['product']->quantity;
                    @endphp
                        <tr>
                            <td>
                                <div class="shopping-cart__product-item">
                                    <img loading="lazy" src="{{ asset('uploads/products/' . $item['image']) }}" width="120" height="120" alt="" />
                                </div>
                            </td>
                            <td>
                                <div class="shopping-cart__product-item__detail">
                                    <h4>{{ $item['product']->name }}</h4>
                                    <ul class="shopping-cart__product-item__options">
                                        <li>Brand: {{ $item['product']->brand->name ?? '-' }}</li>
                                        <li>Category: {{ $item['product']->category->name ?? '-' }}</li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <span class="shopping-cart__product-price">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <div class="qty-control position-relative">
                                    <div class="qty-control__reduce">-</div>
                                    <input type="number"
                                    name="quantity"
                                    min="1" 
                                    max="{{ $remainingStock > 0 ? $remainingStock : 0 }}" 
                                    value="{{ $qtyInCart }}"
                                    class="qty-control__number text-center"
                                    data-id="{{ $item['product']->id }}"
                                    data-stock="{{ $remainingStock }}"
                                    data-prices='@json($item["product"]->product_price)'>
                                    <div class="qty-control__increase">+</div>
                                </div>
                            </td>
                            <td>
                                <span class="shopping-cart__subtotal" id="subtotal-{{ $item['product']->id }}">
                                    Rp{{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('cart.delete') }}" method="POST" class="delete-cart-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $item['product']->id }}">
                                    <a href="#" class="remove-cart" onclick="return confirmDelete(this);">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                                            <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                                        </svg>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="shopping-cart__totals-wrapper">
                <div class="sticky-content">
                    <div class="shopping-cart__totals">
                        <h3>Cart Totals</h3>
                        <table class="cart-totals">
                            <tbody>
                                <tr class="cart-total">
                                    <th>Total</th>
                                    <td id="cart-total">Rp{{ number_format($total, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mobile_fixed-btn_wrapper">
                        <div class="button-wrapper container">
                            <a href="{{route('checkout')}}" class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12 text-center pt-5 pb-5">
                    <p>No item found in your cart</p>
                    <a href="{{route('shop')}}" class="btn btn-info">Shop Now</a>
                </div>
            </div>
        @endif
        </div>
    </section>
</main>

@endsection
@push("scripts")

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cartTable = document.querySelector('.cart-table');


    cartTable.addEventListener('click', function (e) {
        const increaseBtn = e.target.closest('.qty-control__increase');
        const decreaseBtn = e.target.closest('.qty-control__reduce');

        if (!increaseBtn && !decreaseBtn) return;

        const input = (increaseBtn || decreaseBtn).closest('.qty-control').querySelector('input[type="number"]');
        const currentValue = parseInt(input.getAttribute('value')) || parseInt(input.value) || 1;
        let newValue = currentValue;

        if (increaseBtn) {
            newValue = currentValue + 1;
        } else if (decreaseBtn) {
            newValue = currentValue > 1 ? currentValue - 1 : 1;
        }

        input.value = newValue;
        input.setAttribute('value', newValue);

        updateSubtotal(input);
    });

    document.querySelectorAll('.qty-control__number').forEach(input => {
        let debounce;
        input.addEventListener('input', function () {
            clearTimeout(debounce);
            debounce = setTimeout(() => {
                updateSubtotal(input);
            }, 300); // tunggu 300ms setelah user berhenti mengetik
        });

        input.addEventListener('change', function () {
            updateSubtotal(input); // fallback kalau user tekan enter atau keluar field
        });
    });

    function updateSubtotal(input) {
        let quantity = parseInt(input.value) || 1;
        const maxStock = parseInt(input.getAttribute('max')) || Infinity;
        if (quantity > maxStock) quantity = maxStock;

        const productId = input.dataset.id;
        const priceTiers = JSON.parse(input.dataset.prices);

        let applicablePrice = 0;

        priceTiers.forEach(tier => {
            if (quantity >= tier.min_quantity) {
                applicablePrice = tier.price;
            }
        });

        const subtotal = applicablePrice * quantity;

        input.value = quantity;
        input.setAttribute('value', quantity); // untuk menjaga konsistensi

        const subtotalElement = document.getElementById('subtotal-' + productId);
        if (subtotalElement) {
            subtotalElement.textContent = 'Rp' + formatRupiah(subtotal);
        }

        const priceElement = input.closest('tr').querySelector('.shopping-cart__product-price');
        if (priceElement) {
            priceElement.textContent = 'Rp' + formatRupiah(applicablePrice);
        }

        updateCartSession(productId, quantity);
        updateCartTotal();
    }

    function updateCartSession(id, quantity) {
        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: id, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Cart updated:', data);
        })
        .catch(error => {
            console.error('Error updating cart:', error);
        });
    }

    function updateCartTotal() {
        let total = 0;

        document.querySelectorAll('.qty-control__number').forEach(input => {
            const quantity = parseInt(input.value) || 0;
            const priceTiers = JSON.parse(input.dataset.prices);
            let price = 0;

            priceTiers.forEach(tier => {
                if (quantity >= tier.min_quantity) {
                    price = tier.price;
                }
            });

            total += quantity * price;
        });

        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = 'Rp' + formatRupiah(total);
        }
    }

    function formatRupiah(amount) {
        return amount.toLocaleString('id-ID');
    }
});

// Tombol hapus item dari cart
function confirmDelete(el) {
    if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
        el.closest('form').submit();
    }
    return false;
}
</script>


@endpush
