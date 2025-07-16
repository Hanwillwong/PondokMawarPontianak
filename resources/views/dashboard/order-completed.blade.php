@extends('layouts.admin')
@section('container')
<style>
    .table-striped  th:nth-child(1), .table-striped  td:nth-child(1) {
        width: 100px;   
    }
    .table-striped  th:nth-child(2), .table-striped  td:nth-child(2) {
        width: 250px;   
    }
</style>
<div class="main-content-inner">                            
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Completed Orders</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>                                                                           
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Completed Orders</div>
                </li>
            </ul>
        </div>
        
        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name" tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @if(Session::has('status'))
                        <p class="alert alert-success">{{Session::get('status')}}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Total Items</th>
                                <th class="text-center">Purchase Type</th>
                                <th class="text-center">Delivered On</th>
                                <th class="text-center">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $address = $order->user->user_address->first();
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $order->created_at }}</td>
                                    <td class="text-center">{{ $order->user->name ?? '-' }}</td>
                                    <td class="text-center">{{ $address->phone ??  $order->user->phone_number }}</td>
                                    
                                    <td class="text-center">{{ $order->status->label }}</td>
                                    <td class="text-center">{{ $order->order_detail->count() }}</td>
                                    <td class="text-center">{{ $order->purchase_type }}</td>
                                    <td class="text-center">
                                        @if($order->purchase_type == 'delivery')
                                        {{ $address->address ?? '-' }}, {{ $address->city ?? '-' }}, {{ $address->province ?? '-' }}, {{ $address->post_code ?? '-' }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-center">Rp {{ number_format($order->total_price, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.orders.show', $order->id) }}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach                                
                        </tbody>
                    </table>                
                </div>
                <div class="divider">
                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                        {{ $orders->appends(request()->query())->fragment('recent-orders')->links() }}
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@push('scripts')

@endpush