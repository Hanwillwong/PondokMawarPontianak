@extends('layouts.admin')
@section('container')
    <!-- main-content-wrap -->
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{route('admin.index')}}"><div class="text-tiny">Dashboard</div></a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{route('admin.products')}}"><div class="text-tiny">Products</div></a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Add product</div>
                    </li>
                </ul>
            </div>
            <!-- form-add-product -->
            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{route('admin.product.store')}}" >
                @csrf
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="" aria-required="true">


                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error("name") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product slug" name="slug" tabindex="0" value="" aria-required="true">
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error("slug") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="supplier">
                        <div class="body-title mb-10">Supplier <span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select class="" name="supplier_id">
                                <option value="">Choose Supplier</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
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
                                    <option value="{{$category->id}}">{{$category->name}}</option>
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
                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach                                      
                                </select>
                            </div>
                        </fieldset>
                        @error("brand_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    </div>
                    <fieldset class="description">
                        <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                        <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true"></textarea>


                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error("description") <span class="alert alert-danger text-center">{{$message}}</span> @enderror                 
                </div>
                <div class="wg-box">
                    <fieldset>
                        <div class="body-title">Upload images <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" style="display:none">                            
                                <img src="{{asset('images/uploads/upload-17.png')}}" class="effect8" alt="">
                            </div>
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
                        <input class="mb-10" type="text" placeholder="Enter Stock" name="quantity" tabindex="0" value="" aria-required="true">                                              
                    </fieldset>
                    @error("quantity") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                    <fieldset class="name">
                        <div class="body-title mb-10">Minimal Quantity & Price <span class="tf-color-1">*</span></div>
                        
                        <div id="price-group">
                            <!-- Group pertama -->
                            <div class="cols gap22 mb-2 price-item">
                                <div class="mb-3 d-flex gap-2 price-row">
                                    <div class="flex-fill">
                                        <input type="text" name="prices[0][min_qty]" class="form-control" placeholder="Minimal Quantity" required>
                                    </div>
                                    <div class="flex-fill">
                                        <input type="text" name="prices[0][price]" class="form-control" placeholder="Enter Price" required>
                                    </div>
                                    <div>
                                        <button type="button" class="remove-price btn btn-outline-danger align-self-end m-2" style="height: 38px;">−</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol tambah -->
                        <button type="button" class="add-price tf-button mt-2">+ Add Price</button>
                    </fieldset>

                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Add product</button>                                            
                    </div>
                </div>
            </form>
            <!-- /form-add-product -->
        </div>
        <!-- /main-content-wrap -->
    </div>
    <!-- /main-content-wrap -->
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
        let priceIndex = 1;  // Mulai dengan index 1 untuk elemen kedua setelah yang pertama

        // Ketika tombol + Add Price diklik, tambahkan elemen input baru untuk harga
        document.querySelector('.add-price').addEventListener('click', function () {
            const group = document.createElement('div');
            group.classList.add('cols', 'gap22', 'mb-2', 'price-item');
            
            // Gunakan priceIndex sebagai bagian dari nama input untuk memastikan indeks unik
            group.innerHTML = `
                <div class="cols gap22 mb-2 price-item">
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
                </div>
            `;
            
            // Tambahkan elemen input baru ke dalam container price-group
            document.getElementById('price-group').appendChild(group);

            // Tambahkan 1 ke priceIndex setiap kali elemen baru ditambahkan
            priceIndex++;
        });

        // Fungsi untuk menghapus elemen input harga ketika tombol remove diklik
        document.getElementById('price-group').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-price')) {
                e.target.closest('.price-item').remove();
            }
        });
    </script>


@endpush