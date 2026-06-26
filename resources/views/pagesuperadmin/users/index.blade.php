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
                                <h4 class="m-0 text-dark fw-bold">Kelola User (Player)</h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard-superadmin') }}">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Manajemen User</li>
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
                            <h5 class="mb-0 text-dark fw-bold">Daftar Player Game</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="simpletable">
                                    <thead class="table-light text-muted uppercase font-semibold text-xs">
                                        <tr>
                                            <th>#</th>
                                            <th>PLAYER PROFILE</th>
                                            <th>EMAIL</th>
                                            <th>SALDO KOIN</th>
                                            <th>REKOR (MENANG / KALAH)</th>
                                            <th>STATUS</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $index => $u)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $u->foto_profile ?? asset('admin/assets/images/user/avatar-2.jpg') }}" alt="User" class="rounded-circle me-3" style="width: 42px; height: 42px; object-fit: cover;">
                                                        <div>
                                                            <span class="fw-bold text-dark d-block">{{ $u->nama_jalur ?? 'Belum Membuat Jalur' }}</span>
                                                            <span class="text-muted text-xs">Role: {{ ucfirst($u->role) }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $u->email }}</td>
                                                <td>
                                                    <span class="badge bg-light-warning text-warning border border-warning rounded-pill py-1 px-3 fw-bold">
                                                        <i class="ti ti-coin me-1"></i>{{ number_format($u->kuansing_poin) }} KP
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <span class="badge bg-light-success text-success border border-success rounded-pill px-2 py-1">
                                                            🏆 {{ $u->wins_count }} Menang
                                                        </span>
                                                        <span class="badge bg-light-danger text-danger border border-danger rounded-pill px-2 py-1">
                                                            💀 {{ $u->losses_count }} Kalah
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($u->is_blocked)
                                                        <span class="badge bg-light-danger text-danger border border-danger rounded-pill px-3 py-1">BLOCKED</span>
                                                    @else
                                                        <span class="badge bg-light-success text-success border border-success rounded-pill px-3 py-1">ACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('superadmin.users.toggle-block', $u->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status blokir untuk player ini?')">
                                                        @csrf
                                                        @if($u->is_blocked)
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                                                <i class="ti ti-lock-open me-1"></i> Unblock Player
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                                                <i class="ti ti-lock me-1"></i> Block Player
                                                            </button>
                                                        @endif
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">Belum ada player terdaftar.</td>
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
