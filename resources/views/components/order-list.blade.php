@foreach($orders as $order)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <strong>#{{ $order->reference_number }}</strong>
            <span class="badge bg-info text-dark">{{ strtoupper($order->status->label ?? '-') }}</span>
        </div>
        <div class="card-body">
            @foreach($order->order_detail as $item)
                <div class="d-flex mb-3">
                    <img src="{{ asset('uploads/products/' . $item->product->image) }}" alt="Produk" class="img-thumbnail" width="80">
                    <div class="ms-3">
                        <div>{{ $item->product->name }}</div>
                        <div class="text-muted">x{{ $item->quantity }} | Rp{{ number_format($item->price_at_order, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach

            <div class="text-end">
                <strong>Total Pesanan: Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="card-footer text-end">
            @if($order->status->label === 'paid')
                <a href="{{ route('product.show', $item->product->id) }}" class="btn btn-sm btn-outline-primary">Beli Lagi</a>
            @elseif($order->status->label === 'dikirim')
                <button class="btn btn-sm btn-success">Pesanan Selesai</button>
                <button class="btn btn-sm btn-outline-danger">Ajukan Pengembalian</button>
            @elseif($order->status->label === 'pending')
                <button 
                    class="btn btn-sm btn-warning btn-pay-now"
                    data-order-id="{{ $order->id }}"
                    data-snap-token="{{ $order->snap_token }}"
                    data-redirect-url="{{ $order->snap_redirect_url }}">
                    Bayar Sekarang
                </button>

            @endif
        </div>
    </div>
@endforeach
