@extends('template-admin.layout')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title mb-2">
                                <h4 class="m-0 text-dark fw-bold">Riwayat Transaksi Topup</h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard-superadmin') }}">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Transaksi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 text-dark fw-bold">Log Aktivitas Pembayaran QRIS</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="simpletable">
                                    <thead class="table-light text-muted uppercase font-semibold text-xs">
                                        <tr>
                                            <th>#</th>
                                            <th>ORDER ID</th>
                                            <th>PLAYER</th>
                                            <th>NOMINAL</th>
                                            <th>JUMLAH KOIN</th>
                                            <th>STATUS</th>
                                            <th>SIGNATURE</th>
                                            <th>TANGGAL PENGJUAN</th>
                                            <th>WAKTU PEMBAYARAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $index => $tx)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="fw-bold text-dark font-mono text-xs">{{ $tx->order_id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $tx->user->foto_profile ?? asset('admin/assets/images/user/avatar-2.jpg') }}" alt="User" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                        <div>
                                                            <div class="fw-semibold text-dark">{{ $tx->user->nama_jalur ?? 'No Name' }}</div>
                                                            <div class="text-muted text-xs">{{ $tx->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fw-semibold text-dark">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-light-warning text-warning border border-warning rounded-pill py-1 px-3 fw-bold">
                                                        <i class="ti ti-coin me-1"></i>{{ number_format($tx->coin_amount) }} KP
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($tx->status == 'SUCCESS')
                                                        <span class="badge bg-light-success text-success border border-success rounded-pill py-1 px-3 fw-bold">SUCCESS</span>
                                                    @elseif($tx->status == 'PENDING')
                                                        <span class="badge bg-light-warning text-warning border border-warning rounded-pill py-1 px-3 fw-bold">PENDING</span>
                                                    @else
                                                        <span class="badge bg-light-danger text-danger border border-danger rounded-pill py-1 px-3 fw-bold">EXPIRED</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tx->signature)
                                                        <code class="text-xs text-muted font-mono" title="{{ $tx->signature }}">{{ substr($tx->signature, 0, 15) }}...</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted text-xs">{{ $tx->created_at->format('d M Y H:i:s') }}</td>
                                                <td class="text-muted text-xs">
                                                    @if($tx->paid_at)
                                                        <span class="text-success"><i class="ti ti-calendar-event me-1"></i>{{ $tx->paid_at->format('d M Y H:i:s') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi pembayaran.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection
