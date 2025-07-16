@extends('layouts.main')

@push('styles')
<style>
    .nav-tabs {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        flex-wrap: nowrap;
    }

    .nav-tabs .nav-item {
        display: inline-block;
    }

    .nav-tabs .nav-link {
        color: #666;
        opacity: 0.6;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link.active {
        font-weight: bold;
        opacity: 1;
        color: #000;
        border-bottom: 2px solid #000;
        background-color: transparent;
    }

    .nav-tabs::-webkit-scrollbar {
        display: none;
    }

    .nav-tabs {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush


@section('container')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{ route('account.orders') }}" class="menu-link menu-link_us-s">Orders</a></li>
            <li><a href="{{route('pages.account-address')}}" class="menu-link menu-link_us-s">Addresses</a></li>
            <li>
                <form method="POST" action="{{route('logout')}}" id="logout-form">
                    @csrf
                    <a href="{{route('logout')}}" class="" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                </form>
            </li>
          </ul>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__dashboard">

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h4 class="mb-4">Detail Pesanan <span class="text-primary">#{{ $order->reference_number }}</span></h4>

                    {{-- Informasi Utama --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-info">{{ strtoupper($order->status->label) }}</span>
                            </p>
                            <p class="mb-1"><strong>Metode Pembayaran:</strong> 
                                @if ($order->payment_method === 'midtrans' && $midtrans)
                                    {{ strtoupper($midtrans->payment_type) }}
                                    
                                    {{-- Tambahan keterangan untuk VA --}}
                                    @if ($midtrans->payment_type === 'bank_transfer' && isset($midtrans->va_numbers[0]))
                                    - {{ strtoupper($midtrans->va_numbers[0]->bank) }}
                                    @endif

                                @else
                                    {{ strtoupper($order->payment_method) }}
                                @endif
                            </p>
                            <p class="mb-1"><strong>Jenis Pembelian:</strong> {{ strtoupper($order->purchase_type) }}</p>
                        </div>

                        @if($order->address)
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Alamat Pengiriman:</strong></p>
                                <div class="text-muted">
                                    {{ $order->address->name }}<br>
                                    {{ $order->address->address }}<br>
                                    Telp: {{ $order->address->phone }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Informasi Midtrans --}}
                    @if(
                        $order->payment_method === 'midtrans' &&
                        $midtrans &&
                        $midtrans->payment_type === 'bank_transfer' &&
                        $midtrans->transaction_status === 'pending'
                    )
                        <div class="border rounded p-4 mb-4">
                            <h5 class="mb-3">Pembayaran via Transfer Bank</h5>
                            
                            <p class="mb-2"><strong>Bank:</strong> {{ strtoupper($midtrans->va_numbers[0]->bank ?? '-') }}</p>
                            
                            <p class="mb-2"><strong>Nomor Virtual Account:</strong></p>
                            <div class="bg-white p-3 border rounded d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                <span class="fw-bold text-primary text-break w-100 w-sm-auto fs-6 fs-sm-5" id="va-number" style="word-break: break-all;">
                                    {{ $midtrans->va_numbers[0]->va_number ?? '-' }}
                                </span>
                                <button class="btn btn-sm btn-outline-secondary flex-shrink-0" onclick="copyVANumber()">Salin</button>
                            </div>


                            <p class="mt-3 mb-0"><small class="text-muted">Transfer ke nomor VA ini untuk menyelesaikan pembayaran.</small></p>
                            <p class="mb-0"><small class="text-muted">Transaksi dibuat: {{ date('d M Y H:i', strtotime($midtrans->transaction_time)) }}</small></p>
                            @php
                                $expiredTime = \Carbon\Carbon::parse($midtrans->transaction_time)->addMinutes(15); // atau ganti dengan waktu yang kamu tetapkan
                            @endphp
                            <div class="mt-3">
                                <h6 class="mb-1">Batas Waktu Pembayaran:</h6>
                                <p id="countdown" class="fw-bold fs-5">--:--:--</p>
                            </div>
                        </div>

                        <script>
                            function copyVANumber() {
                                const va = document.getElementById('va-number').innerText;
                                navigator.clipboard.writeText(va);
                                alert('Nomor VA berhasil disalin');
                            }
                        </script>
                    @endif

                    {{-- Info Tambahan QRIS atau Gopay jika ada --}}
                    @if(
                        $order->payment_method === 'midtrans' &&
                        $midtrans &&
                        $midtrans->payment_type !== 'bank_transfer' &&
                        $midtrans->transaction_status === 'pending'
                    )
                        <div class="border rounded p-4 bg-light mb-4">
                            <h5 class="mb-3">Info Pembayaran</h5>

                            <p><strong>Jenis:</strong> {{ strtoupper($midtrans->payment_type) }}</p>

                            @if($midtrans->payment_type === 'qris')
                            
                                @if(isset($midtrans->actions) && is_array($midtrans->actions) && isset($midtrans->actions[0]->url))
                                    <p>QRIS: 
                                        <a href="{{ $midtrans->actions[0]->url }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat QR</a>
                                    </p>
                                @else
                                    <p class="">QRIS tidak tersedia atau link tidak ditemukan.</p>
                                @endif
                            
                            @elseif($midtrans->payment_type === 'gopay')
                                <p>Status: {{ $midtrans->transaction_status }}</p>
                            @endif

                            <p class="mb-0"><small class="text-muted">Transaksi dibuat: {{ date('d M Y H:i', strtotime($midtrans->transaction_time)) }}</small></p>
                        </div>
                    @endif
                    
                    <hr>


                    {{-- Produk --}}
                    <h5 class="mb-3">Produk Dipesan</h5>
                    @foreach($order->order_detail as $item)
                        <div class="d-flex mb-3 align-items-center">
                            <img src="{{ asset('uploads/products/' . $item->product->image) }}" alt="Produk" class="img-thumbnail rounded" width="80">
                            <div class="ms-3">
                                <div class="fw-semibold">{{ $item->product->name }}</div>
                                <div class="text-muted small">x{{ $item->quantity }} &bull; Rp{{ number_format($item->price_at_order, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-end mt-4">
                        <h5>Total: Rp{{ number_format($order->total_price, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>


          </div>
        </div>
      </div>
    </section>
  </main>

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    const expireAt = new Date("{{ $expiredTime->format('Y-m-d H:i:s') }}").getTime();

    const countdownInterval = setInterval(function () {
        const now = new Date().getTime();
        const distance = expireAt - now;

        if (distance < 0) {
            clearInterval(countdownInterval);
            document.getElementById("countdown").innerText = "Waktu pembayaran telah berakhir";
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerText =
            String(hours).padStart(2, '0') + ":" +
            String(minutes).padStart(2, '0') + ":" +
            String(seconds).padStart(2, '0');
    }, 1000);
</script>


@endpush