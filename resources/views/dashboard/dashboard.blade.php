@extends('layouts.admin')
@section('container')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="mb-30">
            <div class="row g-3">

                {{-- 1 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                            <div>
                                <div class="body-text mb-2">Ongoing Orders</div>
                                <h4>{{ $ongoingCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg">
                                <div class="image ic-bg d-flex align-items-center justify-content-center fw-bold" style="font-size: 18px; color:#2275fc;">
                                    Rp
                                </div>
                            </div>
                            <div>
                                <div class="body-text mb-2">Ongoing Amount</div>
                                <h4>Rp {{ number_format($ongoingAmount, 0) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                            <div>
                                <div class="body-text mb-2">Delivery Orders</div>
                                <h4>{{ $deliveryCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg">
                                <div class="image ic-bg d-flex align-items-center justify-content-center fw-bold" style="font-size: 18px; color:#2275fc;">
                                    Rp
                                </div>
                            </div>
                            <div>
                                <div class="body-text mb-2">Delivery Orders Amount</div>
                                <h4>Rp {{ number_format($deliveryAmount, 0) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                            <div>
                                <div class="body-text mb-2">Completed Orders</div>
                                <h4>{{ $completedCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg">
                                <div class="image ic-bg d-flex align-items-center justify-content-center fw-bold" style="font-size: 18px; color:#2275fc;">
                                    Rp
                                </div>
                            </div>
                            <div>
                                <div class="body-text mb-2">Completed Orders</div>
                                <h4>Rp {{ number_format($completedAmount, 0) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 7 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                            <div>
                                <div class="body-text mb-2">Pickup Orders</div>
                                <h4>{{ $pickupCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 8 --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="wg-chart-default h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="image ic-bg">
                                <div class="image ic-bg d-flex align-items-center justify-content-center fw-bold" style="font-size: 18px; color:#2275fc;">
                                    Rp
                                </div>
                            </div>
                            <div>
                                <div class="body-text mb-2">Pickup Orders Amount</div>
                                <h4>Rp {{ number_format($pickupAmount, 0) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Recent orders</h5>
                    <div>
                        <a href="{{ route('admin.index', ['type' => 'pickup']) }}" class="btn btn-outline-primary {{ request('type') === 'pickup' ? 'active' : '' }}">
                            Pickup ({{$pickupCount}})
                        </a>
                        <a href="{{ route('admin.index', ['type' => 'delivery']) }}" class="btn btn-outline-success {{ request('type') === 'delivery' ? 'active' : '' }}">
                            Delivery ({{$deliveryCount}})
                        </a>
                        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary {{ is_null(request('type')) ? 'active' : '' }}">
                            All ({{$totalCount}})
                        </a>
                    </div>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="recent-orders">
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
                                         $address = $order->address;
                                    @endphp
                                    <tr>
                                        <a href="{{ route('admin.orders.show', $order->id) }}">
                                        <td class="text-center">{{ $order->created_at }}</td>
                                        <td class="text-center">{{ $address->name ??  $order->user->name }}</td>
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
                                    </a>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $orders->appends(request()->query())->fragment('recent-orders')->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection