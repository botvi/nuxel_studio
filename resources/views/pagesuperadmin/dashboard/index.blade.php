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
                                <h4 class="m-0 text-dark fw-bold">Dashboard Admin</h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard-superadmin') }}">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- Stat Cards -->
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7);">
                        <div class="card-body p-4 text-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="m-0 text-white-50 text-uppercase fw-semibold tracking-wider">Total Player</h6>
                                <div class="bg-white-10 p-2 rounded-3 text-white"><i class="ti ti-users f-24"></i></div>
                            </div>
                            <h2 class="mb-1 text-white fw-bold">{{ number_format($totalUsers) }}</h2>
                            <p class="mb-0 text-white-50 text-sm">Terdaftar dalam game</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #198754, #157347);">
                        <div class="card-body p-4 text-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="m-0 text-white-50 text-uppercase fw-semibold tracking-wider">Pendapatan QRIS</h6>
                                <div class="bg-white-10 p-2 rounded-3 text-white"><i class="ti ti-wallet f-24"></i></div>
                            </div>
                            <h2 class="mb-1 text-white fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                            <p class="mb-0 text-white-50 text-sm">Total transaksi SUCCESS</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                        <div class="card-body p-4 text-dark">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="m-0 text-dark-50 text-uppercase fw-semibold tracking-wider">Koin Terjual (KP)</h6>
                                <div class="bg-dark-10 p-2 rounded-3 text-dark"><i class="ti ti-coin f-24"></i></div>
                            </div>
                            <h2 class="mb-1 text-dark fw-bold">{{ number_format($totalCoinsSold) }} KP</h2>
                            <p class="mb-0 text-dark-50 text-sm">Kuansing Poin dibeli player</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #dc3545, #bb2d3b);">
                        <div class="card-body p-4 text-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="m-0 text-white-50 text-uppercase fw-semibold tracking-wider">Player Diblokir</h6>
                                <div class="bg-white-10 p-2 rounded-3 text-white"><i class="ti ti-user-off f-24"></i></div>
                            </div>
                            <h2 class="mb-1 text-white fw-bold">{{ number_format($blockedUsers) }}</h2>
                            <p class="mb-0 text-white-50 text-sm">Status blocked active</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders / Transactions -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark fw-bold">Transaksi Terbaru</h5>
                            <a href="{{ route('superadmin.transactions') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted uppercase font-semibold text-xs">
                                        <tr>
                                            <th class="ps-4">ORDER ID</th>
                                            <th>PLAYER</th>
                                            <th>NOMINAL</th>
                                            <th>KOIN (KP)</th>
                                            <th>STATUS</th>
                                            <th class="pe-4 text-end">TANGGAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $tx)
                                            <tr>
                                                <td class="ps-4 fw-semibold text-dark">{{ $tx->order_id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $tx->user->foto_profile ?? asset('admin/assets/images/user/avatar-2.jpg') }}" alt="User" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                        <div>
                                                            <div class="fw-semibold text-dark">{{ $tx->user->nama_jalur ?? 'No Name' }}</div>
                                                            <div class="text-muted text-xs">{{ $tx->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                                <td class="text-warning fw-semibold">+{{ number_format($tx->coin_amount) }} KP</td>
                                                <td>
                                                    @if($tx->status == 'SUCCESS')
                                                        <span class="badge bg-light-success text-success border border-success rounded-pill py-1 px-3">SUCCESS</span>
                                                    @elseif($tx->status == 'PENDING')
                                                        <span class="badge bg-light-warning text-warning border border-warning rounded-pill py-1 px-3">PENDING</span>
                                                    @else
                                                        <span class="badge bg-light-danger text-danger border border-danger rounded-pill py-1 px-3">EXPIRED</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 text-end text-muted">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi pembayaran.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leaderboard Coins / Top Players -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark fw-bold">Leaderboard Koin Player</h5>
                            <a href="{{ route('superadmin.users') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Detail</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($topPlayers as $index => $player)
                                    <div class="list-group-item d-flex align-items-center justify-content-between border-0 py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-3 text-muted" style="width: 20px;">#{{ $index + 1 }}</span>
                                            <img src="{{ $player->foto_profile ?? asset('admin/assets/images/user/avatar-2.jpg') }}" alt="User" class="rounded-circle me-3" style="width: 38px; height: 38px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 text-dark fw-semibold">{{ $player->nama_jalur ?? 'No Name' }}</h6>
                                                <span class="text-muted text-xs">{{ $player->email }}</span>
                                            </div>
                                        </div>
                                        <span class="badge bg-light-warning text-warning border border-warning rounded-pill py-1 px-3 fw-bold">
                                            <i class="ti ti-coin me-1"></i>{{ number_format($player->kuansing_poin) }} KP
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">Belum ada data player.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <style>
        .bg-white-10 {
            background-color: rgba(255, 255, 255, 0.15);
        }
        .bg-dark-10 {
            background-color: rgba(0, 0, 0, 0.08);
        }
        .text-xs {
            font-size: 0.75rem;
        }
        .text-dark-50 {
            color: rgba(0, 0, 0, 0.5);
        }
        .tracking-wider {
            letter-spacing: 0.05em;
        }
    </style>
@endsection
