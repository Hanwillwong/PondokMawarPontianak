@extends('layouts.main')
@section('container')

<main class="pt-90">
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container">
        <div class="row">
        <div class="col-lg-7">
            <div class="product-single__media" data-media-type="vertical-thumbnail">
            <div class="product-single__image">
                <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{asset('uploads/products')}}/{{$product->image}}" width="674"
                        height="674" alt="" @if ($product->quantity == 0)
                            style="filter: grayscale(100%) brightness(0.7);" 
                            class="opacity-50"
                        @endif>
                    <a data-fancybox="gallery" href="{{asset('uploads/products')}}/{{$product->image}}" data-bs-toggle="tooltip"
                        data-bs-placement="left" title="Zoom">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_zoom" />
                        </svg>
                    </a>
                    </div>
                </div>
                </div>
            </div>  
            </div>
        </div>
        <div class="col-lg-5">
            <div class="d-flex justify-content-between mb-4 pb-md-2">
            <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                <a href="/" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                <a href="" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
            </div><!-- /.breadcrumb -->
            
            </div>
            <h1 class="product-single__name">{{ $product->name }}</h1>
            <div class="product-single__meta-info">
            <!-- <div class="meta-item">
                <label>Category:</label>
                <span>{{$product->category->name ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <label>Brand:</label>
                <span>{{$product->brand->name ?? '-' }}</span>
            </div> -->
            </div>
            <div class="product-single__price" style="margin-top:-10px;margin-bottom:10px;">
                <span class="current-price fw-bold">Rp. {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
            

            <div class="product-pricing-list">
                @foreach ($product->product_price as $price)
                    @if ($price->min_quantity > 1)
                        <div class="price-item">
                            <span class="me-2">Min Qty: {{ $price->min_quantity }}</span>
                            <span class="fw-bold">
                                Rp. {{ number_format($price->price, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
            <span class="me-2">Stock: {{ $product->quantity }}</span>

            <div class="product-single__short-desc mt-4">
            <p>{{ $product->description }}</p>
            </div>
            @php
                $remainingStock = $product->quantity - $qtyInCart;
            @endphp

            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="product-single__addtocart">
                    <div class="qty-control position-relative">
                        <input 
                            type="number" 
                            id="quantity"
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="{{ $remainingStock > 0 ? $remainingStock : 0 }}" 
                            data-stock="{{ $remainingStock > 0 ? $remainingStock : 0 }}" 
                            class="qty-control__number text-center"
                            {{ $remainingStock <= 0 ? 'disabled' : '' }}
                        >
                        <div class="qty-control__reduce">-</div>
                        <div class="qty-control__increase">+</div>
                    </div><!-- .qty-control -->
                    <button type="submit" class="btn btn-primary btn-addtocart" {{ $remainingStock <= 0 ? 'disabled' : '' }}>
                        Add to Cart
                    </button>
                </div>
            </form>

            @if ($remainingStock <= 0)
                <p class="mt-2" style="color: red;">Stok telah habis</p>
            @endif
            
        </div>
        </div>
    </section>
    <section class="products-carousel container mt-5">
        <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>

        <div id="related_products" class="position-relative">
        <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
                "el": "#related_products .products-pagination",
                "type": "bullets",
                "clickable": true
            },
            "navigation": {
                "nextEl": "#related_products .products-carousel__next",
                "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
                "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
                },
                "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
                },
                "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
                }
            }
            }'>
            <div class="swiper-wrapper">
                @forelse ($relatedProducts as $related)
                    <div class="swiper-slide product-card">
                        <div class="pc__img-wrapper position-relative">

                            {{-- Label Stok Habis --}}
                            @if ($related->quantity == 0)
                                <div style="z-index: 999; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"
                                    class="bg-secondary text-white px-3 py-1 rounded-pill small fw-bold text-center">
                                    Stok Habis
                                </div>
                            @endif

                            <a href="{{ route('product.show', $related->id) }}">
                                <img 
                                    loading="lazy" 
                                    src="{{ asset('uploads/products') }}/{{ $related->image }}" 
                                    width="330" 
                                    height="400" 
                                    alt="{{ $related->name }}"
                                    class="pc__img w-100 @if($related->quantity == 0) opacity-50 @endif"
                                    @if($related->quantity == 0)
                                        style="filter: grayscale(100%) brightness(0.7);"
                                    @endif
                                >
                            </a>
                        </div>

                        <div class="pc__info position-relative">
                            <p class="pc__category">{{ $related->category->name }}</p>
                            <h6 class="pc__title">
                                <a href="{{ route('product.show', $related->id) }}">{{ $related->name }}</a>
                            </h6>
                            <div class="product-card__price d-flex">
                                <span class="money price">Rp {{ number_format($related->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No related products found.</p>
                @endforelse

            </div><!-- /.swiper-wrapper -->
        </div><!-- /.swiper-container js-swiper-slider -->

        <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
            <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_prev_md" />
            </svg>
        </div><!-- /.products-carousel__prev -->
        <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
            <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_next_md" />
            </svg>
        </div><!-- /.products-carousel__next -->

        <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
        <!-- /.products-pagination -->
        </div><!-- /.position-relative -->

    </section><!-- /.products-carousel container -->
</main>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('quantity');
    const currentPriceEl = document.getElementById('current-price');
    const btnPlus = document.querySelector('.qty-control__increase');
    const btnMinus = document.querySelector('.qty-control__reduce');
    const maxStock = parseInt(qtyInput.dataset.stock);
    const defaultPrice = parseInt(currentPriceEl.dataset.defaultPrice);
    const priceTiers = [];

    document.querySelectorAll('#price-tiers .price-item').forEach(el => {
        const min = parseInt(el.dataset.min);
        const price = parseInt(el.dataset.price);
        priceTiers.push({ min, price });
    });

    function updatePrice() {
        const qty = parseInt(qtyInput.value);
        let activePrice = defaultPrice;

        priceTiers.sort((a, b) => b.min - a.min).forEach(tier => {
            if (qty >= tier.min) {
                activePrice = tier.price;
                return;
            }
        });

        currentPriceEl.innerText = 'Rp. ' + activePrice.toLocaleString('id-ID');
    }

    function updateButtons() {
        const qty = parseInt(qtyInput.value);
        btnMinus.disabled = qty <= 1;
        btnPlus.disabled = qty >= maxStock;
    }

    btnPlus.addEventListener('click', function () {
        let current = parseInt(qtyInput.value);
        if (current < maxStock) {
            qtyInput.value = current + 1;
            updatePrice();
            updateButtons();
        }
    });

    btnMinus.addEventListener('click', function () {
        let current = parseInt(qtyInput.value);
        if (current > 1) {
            qtyInput.value = current - 1;
            updatePrice();
            updateButtons();
        }
    });

    qtyInput.addEventListener('input', function () {
        updatePrice();
        updateButtons();
    });

    updatePrice();
    updateButtons();
});

</script>
@endpush