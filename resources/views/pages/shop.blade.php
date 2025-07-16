@extends('layouts.main')
@section('container')

<main class="pt-90">
    <section class="shop-main container d-flex pt-2">
        <form method="GET" action="{{ route('shop') }}" id="filter-form">
            <div class="shop-sidebar side-sticky bg-body" id="shopFilter">
                {{-- Header filter untuk mobile --}}
                <div class="aside-header d-flex d-lg-none align-items-center">
                    <h3 class="text-uppercase fs-6 mb-0">Filter By</h3>
                    <button class="btn-close-lg js-close-aside btn-close-aside ms-auto"></button>
                </div>

                <div class="pt-4 pt-lg-0"></div>

                {{-- Filter: Categories --}}
                <div class="accordion" id="categories-list">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-1">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button" data-bs-toggle="collapse"
                                data-bs-target="#accordion-filter-1" aria-expanded="true" aria-controls="accordion-filter-1">
                                Product Categories
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-1" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-1" data-bs-parent="#categories-list">
                            <div class="accordion-body px-0 pb-0 pt-3">
                                <ul class="list list-inline mb-0">
                                    @foreach ($categories as $category)
                                        <li class="list-item">
                                            <div class="form-check d-flex justify-content-between">
                                                <div>
                                                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                        {{ in_array($category->id, request()->input('categories', [])) ? 'checked' : '' }}
                                                        onchange="document.getElementById('filter-form').submit()">
                                                    <label class="form-check-label">{{ $category->name ?? '-' }}</label>
                                                </div>
                                                <span class="text-muted">({{ $category->products_count }})</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter: Brands --}}
                <div class="accordion" id="brand-filters">
                    <div class="accordion-item mb-2 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-brand">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button" data-bs-toggle="collapse"
                                data-bs-target="#accordion-filter-brand" aria-expanded="true" aria-controls="accordion-filter-brand">
                                Brands
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-brand" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-brand" data-bs-parent="#brand-filters">
                            <div class="search-field multi-select accordion-body px-0 pb-0">
                                <ul class="multi-select__list list-unstyled">
                                    @foreach ($brands as $brand)
                                        <li class="list-item">
                                            <div class="form-check d-flex justify-content-between align-items-center">
                                                <div>
                                                    <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}"
                                                        {{ in_array($brand->id, request()->input('brands', [])) ? 'checked' : '' }}
                                                        onchange="document.getElementById('filter-form').submit()">
                                                    <label class="form-check-label">{{ $brand->name ?? '-' }}</label>
                                                </div>
                                                <span class="text-muted">({{ $brand->products_count }})</span>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Optional: Reset Button --}}
                <div class="mt-1">
                    <a href="{{ route('shop') }}" class="btn btn-sm btn-secondary">Reset Filter</a>
                </div>
            </div>
            <input type="hidden" name="sort" id="sort-hidden" value="{{ request('sort') }}">
        </form>


        <div class="shop-list flex-grow-1">
            <div class="d-flex justify-content-between mb-4 pb-md-2">
                <div class="shop-acs d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
                    <select id="sort-select" class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0" aria-label="Sort Items">
                        <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Default Sorting</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Alphabetically, A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Alphabetically, Z-A</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price, low to high</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price, high to low</option>
                    </select>
                    <div class="shop-asc__seprator mx-3 bg-light d-none d-md-block order-md-0">
                        
                    </div>
               
                    <div class="col-size align-items-center order-1 d-none d-lg-flex">
                        <span class="text-uppercase fw-medium me-2">View</span>
                        <button class="btn-link fw-medium me-2 js-cols-size" data-target="products-grid" data-cols="2">2</button>
                        <button class="btn-link fw-medium me-2 js-cols-size" data-target="products-grid" data-cols="3">3</button>
                        <button class="btn-link fw-medium js-cols-size" data-target="products-grid" data-cols="4">4</button>
                    </div>

                    <div class="shop-filter d-flex align-items-center order-0 order-md-3 d-lg-none">
                        <button class="btn-link btn-link_f d-flex align-items-center ps-0 js-open-aside" data-aside="shopFilter">
                        <svg class="d-inline-block align-middle me-2" width="14" height="10" viewBox="0 0 14 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_filter" />
                        </svg>
                        <span class="text-uppercase fw-medium d-inline-block align-middle">Filter</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">

                <!-- Product Default -->
                @foreach ($products as $product)
                <div class="product-card-wrapper">
                <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                    <div class="pc__img-wrapper">
                    <div class="swiper-container background-img js-swiper-slider" data-settings='{"resizeObserver": true}'>
                        <div class="swiper-wrapper">
                        <div class="swiper-slide">

                            <a href="{{ route('product.show', $product->id) }}"><img loading="lazy" src="{{asset('uploads/products')}}/{{$product->image}}" width="330"
                                height="400" alt="Cropped Faux leather Jacket" class="pc__img"></a>
                        </div>
                        </div>
                    </div>
                    <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    </form>
                    </div>

                    <div class="pc__info position-relative">
                    <p class="pc__category">{{$product->category->name ?? '-' }}</p>
                    <h6 class="pc__title"><a href="details.html">{{$product->name}}</a></h6>
                    <div class="product-card__price d-flex">
                        <span class="money price">Rp. {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    </div>
                </div>
                </div>
                @endforeach


            </div>

            <div class="shop-pages d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </section>
</main>

@endsection

@push('scripts')
<script>
    document.getElementById('sort-select').addEventListener('change', function () {
        document.getElementById('sort-hidden').value = this.value;
        document.getElementById('filter-form').submit();
    });
</script>
@endpush
