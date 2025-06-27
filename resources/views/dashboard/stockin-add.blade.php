@extends('layouts.admin')
@section('container')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Stock In information</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.stockin')}}"><div class="text-tiny">Stock In</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Stock In</div>
                </li>
            </ul>
        </div>
        
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.stockin.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="stockin">
                    <div class="body-title">Product <span class="tf-color-1">*</span></div>
                    <div class="select w-full">
                        <select class="w-full" name="product_id">
                            <option value="">Choose product</option>
                            @foreach ($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach                                                                 
                        </select>
                    </div>
                </fieldset>
                @error("product_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="stockin">
                    <div class="body-title">Supplier <span class="tf-color-1">*</span></div>
                    <div class="select w-full">
                        <select class="w-full" name="supplier_id">
                            <option value="">Choose product</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{$product->id}}">{{$supplier->name}}</option>
                            @endforeach                                                                 
                        </select>   
                    </div>
                </fieldset>
                @error("supplier_id") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="quantity">
                    <div class="body-title">Quantity <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="number" placeholder="Quantity" name="quantity">                    
                </fieldset>
                @error("quantity") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="stockin">
                    <div class="body-title">Date <span class="tf-color-1">*</span></div>
                    <input type="date" name="date" class="w-full" value="{{ old('date', date('Y-m-d')) }}">
                </fieldset>
                @error("date") <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
        
    </div>
    
</div>                    
</div>
@endsection
