@extends('layouts.admin')
@section('container')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Product</h3>
        </div>

        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update', $product->id) }}">
            @csrf
            @method('PUT') <!-- penting untuk update -->

            <div class="wg-box">
                <!-- Nama -->
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" placeholder="Enter product name">
                </fieldset>
                @error("name") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <!-- Slug -->
                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="Enter product slug">
                </fieldset>
                @error("slug") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <!-- Supplier -->
                <fieldset class="supplier">
                    <div class="body-title mb-10">Supplier</div>
                    <select name="supplier_id">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $supplier->id == $product->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </fieldset>
                @error("supplier_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <!-- Category & Brand -->
                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category</div>
                        <select name="category_id">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand</div>
                        <select name="brand_id">
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                </div>

                <!-- Description -->
                <fieldset class="description">
                    <div class="body-title mb-10">Description</div>
                    <textarea name="description">{{ old('description', $product->description) }}</textarea>
                </fieldset>

                <!-- Gambar -->
                <fieldset>
                    <div class="body-title">Upload image</div>
                    @if($product->image)
                        <div><img src="{{ asset('storage/' . $product->image) }}" alt="" width="100px"></div>
                    @endif
                    <input type="file" name="image">
                </fieldset>

                <!-- Stok -->
                <fieldset class="name">
                    <div class="body-title mb-10">Stock</div>
                    <input type="text" name="quantity" value="{{ old('quantity', $product->quantity) }}" placeholder="Enter Stock">
                </fieldset>

                <!-- Price -->
                <fieldset>
                    <div class="body-title mb-10">Minimal Quantity & Price</div>
                    <div id="price-group">
                        @foreach ($product->prices as $index => $price)
                            <div class="cols gap22 mb-2 price-item">
                                <div class="mb-3 d-flex gap-2 price-row">
                                    <div class="flex-fill">
                                        <input type="text" name="prices[{{ $index }}][min_qty]" value="{{ $price->min_qty }}" class="form-control" required>
                                    </div>
                                    <div class="flex-fill">
                                        <input type="text" name="prices[{{ $index }}][price]" value="{{ $price->price }}" class="form-control" required>
                                    </div>
                                    <div>
                                        <button type="button" class="remove-price btn btn-outline-danger m-2" style="height: 38px;">−</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-price tf-button mt-2">+ Add Price</button>
                </fieldset>

                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update product</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="price-wrapper" data-count="{{ $product->prices->count() }}"></div>
@endsection

@push("scripts")
<script>
    const wrapper = document.getElementById('price-wrapper');
    let priceIndex = parseInt(wrapper.dataset.count);

    document.querySelector('.add-price').addEventListener('click', function () {
        const group = document.createElement('div');
        group.classList.add('cols', 'gap22', 'mb-2', 'price-item');
        group.innerHTML = `
            <div class="mb-3 d-flex gap-2 price-row">
                <div class="flex-fill">
                    <input type="text" name="prices[${priceIndex}][min_qty]" class="form-control" placeholder="Minimal Quantity" required>
                </div>
                <div class="flex-fill">
                    <input type="text" name="prices[${priceIndex}][price]" class="form-control" placeholder="Enter Price" required>
                </div>
                <div>
                    <button type="button" class="remove-price btn btn-outline-danger align-self-end m-2" style="height: 38px;">−</button>
                </div>
            </div>
        `;
        document.getElementById('price-group').appendChild(group);
        priceIndex++;
    });

    document.getElementById('price-group').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-price')) {
            e.target.closest('.price-item').remove();
        }
    });
</script>
@endpush
