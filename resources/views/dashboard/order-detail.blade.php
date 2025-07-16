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
    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0 fw-semibold">Order Detail #{{ $order->id }}</h4>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    Back
                </a>
            </div>

            <div class="card-body bg-light-subtle">
                {{-- Informasi Pelanggan --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded shadow-sm h-100">
                            <h6 class="text-muted mb-2">Customer Info</h6>
                            @php $addr = $order->address; @endphp
                            <p class="mb-1"><strong>Name:</strong> {{ $addr->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> 0{{ $addr->phone ?? '-' }}</p>
                            <p class="mb-0"><strong>Type:</strong> {{ ucfirst($order->purchase_type) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded shadow-sm h-100">
                            <h6 class="text-muted mb-2">Delivery Address</h6>
                            @php $addr = $order->user->user_address->first(); @endphp
                            <p class="mb-1">{{ $addr->address ?? '-' }}</p>
                            <p class="mb-1">{{ $addr->city ?? '-' }}, {{ $addr->province ?? '-' }}</p>
                            <p class="mb-0">Postcode: {{ $addr->post_code ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Update Status --}}
                <div class="bg-white p-4 rounded shadow-sm mb-4">
                    <h6 class="mb-3 text-muted">Update Order Status</h6>
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-6">
                            <select name="status_id" id="status_id" class="mt-2">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn-success">Update</button>
                        </div>
                    </form>
                </div>

                {{-- Item Order --}}
                <div class="bg-white p-4 rounded shadow-sm">
                    <h6 class="mb-3 text-muted">Ordered Products</h6>
                    <div class="">
                        <table class="table table-bordered align-middle ">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->order_detail as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($item->product->price ?? 0, 2) }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($item->price_at_order * $item->quantity, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end text-primary">Rp {{ number_format($order->total_price, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(function(){
            $(".delete").on('click',function(e){
                e.preventDefault();
                var selectedForm = $(this).closest('form');
                swal({
                    title: "Are you sure?",
                    text: "You want to delete this record?",
                    type: "warning",
                    buttons: ["No!", "Yes!"],
                    confirmButtonColor: '#dc3545'
                }).then(function (result) {
                    if (result) {
                        selectedForm.submit();  
                    }
                });                             
            });
        });
    </script>
@endpush