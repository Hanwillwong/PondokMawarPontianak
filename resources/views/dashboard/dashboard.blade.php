@extends('layouts.admin')
@section('container')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="tf-section-2 mb-30">
            <div class="flex gap20 flex-wrap-mobile">
                <div class="w-half">

                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Ongoing Orders</div>
                                    <h4>{{ $totalCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Total Amount</div>
                                    <h4>Rp {{ number_format($totalAmount, 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Delivery Orders</div>
                                    <h4>{{$deliveryCount}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Delivery Orders Amount</div>
                                    <h4>Rp {{number_format($deliveryAmount,0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="w-half">

                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Completed Orders</div>
                                    <h4>{{$completedCount}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Completed Orders Amount</div>
                                    <h4>Rp {{number_format($completedAmount,0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Pickup Orders</div>
                                    <h4>{{$pickupCount}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg">
                                    <i class="icon-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Pickup Orders Amount</div>
                                    <h4>Rp {{ number_format($pickupAmount,0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Earnings revenue</h5>
                    <div class="dropdown default">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <span class="icon-more"><i class="icon-more-horizontal"></i></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="javascript:void(0);">This Week</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">Last Week</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex flex-wrap gap40">
                    <div>
                        <div class="mb-2">
                            <div class="block-legend">
                                <div class="dot t1"></div>
                                <div class="text-tiny">Revenue</div>
                            </div>
                        </div>
                        <div class="flex items-center gap10">
                            <h4>$37,802</h4>
                            <div class="box-icon-trending up">
                                <i class="icon-trending-up"></i>
                                <div class="body-title number">0.56%</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2">
                            <div class="block-legend">
                                <div class="dot t2"></div>
                                <div class="text-tiny">Order</div>
                            </div>
                        </div>
                        <div class="flex items-center gap10">
                            <h4>$28,305</h4>
                            <div class="box-icon-trending up">
                                <i class="icon-trending-up"></i>
                                <div class="body-title number">0.56%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="line-chart-8"></div>
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
                                        $address = $order->user->user_address->first();
                                    @endphp
                                    <tr>
                                        <a href="{{ route('admin.orders.show', $order->id) }}">
                                        <td class="text-center">{{ $order->created_at }}</td>
                                        <td class="text-center">{{ $order->user->name ?? '-' }}</td>
                                        <td class="text-center">0{{ $address->phone ?? '-' }}</td>
                                        
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