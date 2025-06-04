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
            <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{ old('name', $product->name) }}" aria-required="true">


                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error("name") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product slug" name="slug" tabindex="0" value="{{ old('slug', $product->slug) }}" aria-required="true">
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error("slug") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="supplier">
                        <div class="body-title mb-10">Supplier <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select class="" name="supplier_id">
                                <option value="">Choose Supplier</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $supplier->id == $product->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach                                                                 
                            </select>
                        </div>
                    </fieldset>
                    @error("supplier_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <div class="gap22 cols">
                        <fieldset class="category">
                            <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select class="" name="category_id">
                                    <option value="">Choose category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach                                                                 
                                </select>
                            </div>
                        </fieldset>
                        @error("category_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                        <fieldset class="brand">
                            <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select class="" name="brand_id">
                                    <option value="">Choose Brand</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach                                      
                                </select>
                            </div>
                        </fieldset>
                        @error("brand_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    </div>
                    <fieldset class="description">
                        <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                            <textarea name="description">{{ old('description', $product->description) }}</textarea>
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error("description") <span class="alert alert-danger text-center">{{$message}}</span> @enderror                 
                </div>
                <div class="wg-box">
                    <fieldset>
                        <div class="body-title">Upload images <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            @if($product->image)
                                <div class="item" id="imgpreview">
                                    <img src="{{ asset('uploads/products/' . $product->image) }}" class="effect8" alt="Product Image">
                                </div>
                            @endif
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </fieldset> 
                    
                    @error("image") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Stock <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter Stock"  value="{{ old('quantity', $product->quantity) }}" name="quantity" tabindex="0" value="" aria-required="true">                                              
                    </fieldset>
                    @error("quantity") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                    <fieldset class="name">
                        <div class="body-title mb-10">Minimal Quantity & Price</div>

                        {{-- Wrapper dengan id dan data-count --}}
                        <div id="price-group" data-count="{{ $product->product_price ? $product->product_price->count() : 0 }}">
                            @foreach ($product->product_price ?? [] as $index => $price)
                                <div class="cols gap22 mb-2 price-item">
                                    <div class="mb-3 d-flex gap-2 price-row">
                                        <div class="flex-fill">
                                            <input type="text" name="prices[{{ $index }}][min_qty]" value="{{ $price->min_quantity }}" class="form-control" placeholder="Minimal Quantity" required>
                                        </div>
                                        <div class="flex-fill">
                                            <input type="text" name="prices[{{ $index }}][price]" value="{{ $price->price }}" class="form-control" placeholder="Enter Price" required>
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
@endsection

@push("scripts")
<script>
        $(function(){
            $("#myFile").on("change",function(e){
                const photoInp = $("#myFile");                    
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src',URL.createObjectURL(file));
                    $("#imgpreview").show();                        
                }
            });
            $("#gFile").on("change",function(e){
                $(".gitems").remove();
                const gFile = $("gFile");
                const gphotos = this.files;                    
                $.each(gphotos,function(key,val){                        
                    $("#galUpload").prepend(`<div class="item gitems"><img src="${URL.createObjectURL(val)}" alt=""></div>`);                        
                });                    
            });
            $("input[name='name']").on("input",function(){
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });
            
        });
        function StringToSlug(Text) {
            return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
        }      
</script>

<script>
    const priceGroup = document.getElementById('price-group');
    let priceIndex = parseInt(priceGroup.dataset.count || 0);

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
        priceGroup.appendChild(group);
        priceIndex++;
    });

    priceGroup.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-price')) {
            e.target.closest('.price-item').remove();
        }
    });
</script>

@endpush
