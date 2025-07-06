@foreach($orders as $order)
    <a href="{{ route('account.order.details', $order->id) }}" class="text-decoration-none text-dark">
        <div class="card mb-4 border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>#{{ $order->reference_number }}</strong>
                    <span class="badge bg-secondary ms-2">{{ ucfirst($order->purchase_type) }}</span>
                </div>
                <span class="badge bg-info text-dark">{{ strtoupper($order->status->label ?? '-') }}</span>
            </div>

            <div class="card-body">
                @foreach($order->order_detail as $item)
                    <div class="d-flex mb-3">
                        <img src="{{ asset('uploads/products/' . $item->product->image) }}" alt="Produk" class="img-thumbnail rounded" width="80">
                        <div class="ms-3">
                            <div class="fw-semibold">{{ $item->product->name }}</div>
                            <div class="text-muted small">
                                x{{ $item->quantity }} | Rp{{ number_format($item->price_at_order, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="text-end mt-3">
                    <strong>Total Pesanan: Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="card-footer bg-white text-end">
                @if($order->status->label === 'paid')
                    <a href="{{ route('product.show', $item->product->id) }}" class="btn btn-sm btn-outline-primary">Beli Lagi</a>
                @elseif($order->status->label === 'delivered')
                    <button class="btn btn-sm btn-success">Pesanan Selesai</button>
                    <button class="btn btn-sm btn-outline-danger">Ajukan Pengembalian</button>
                @elseif($order->status->label === 'pending')
                    <a href="{{ route('account.order.details', $order->id) }}" class="btn btn-sm btn-warning btn-pay-now">Bayar Sekarang</a>
                @endif
            </div>
        </div>
    </a>
@endforeach
