@extends('layouts.admin')
@section('container')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Supplier information</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.suppliers')}}"><div class="text-tiny">Suppliers</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Supplier</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.supplier.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Supplier Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Supplier Name" name="name" tabindex="0" value="{{old('name')}}" aria-required="true">                    
                </fieldset>
                @error("name") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">Supplier Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Supplier Slug" name="slug" tabindex="0" value="{{old('slug')}}" aria-required="true">                                       
                </fieldset>
                @error("slug") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="phone_number">
                    <div class="body-title">Supplier Phone Number<span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Supplier Phone Number" name="phone_number" tabindex="0" value="{{old('name')}}" aria-required="true">                    
                </fieldset>
                @error("phone_number") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="address">
                    <div class="body-title">Supplier Address<span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Supplier Address" name="address" tabindex="0" value="{{old('name')}}" aria-required="true">                    
                </fieldset>
                @error("address") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
        <!-- /new-category -->
    </div>
    <!-- /main-content-wrap -->
</div>                    
</div>
@endsection

@push("scripts")
    <script>
        $(function(){
            $("input[name='name']").on("input", function(){
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });
        });
        function StringToSlug(Text) {
            return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
        }      
    </script>
@endpush