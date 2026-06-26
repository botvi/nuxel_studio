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
                                <h4 class="m-0 text-dark fw-bold">Manajemen Paket Koin</h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard-superadmin') }}">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Paket Koin & Harga</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- Add Package Form -->
                <div class="col-md-4 col-sm-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 text-dark fw-bold">Tambah Paket Baru</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('superadmin.packages.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="coin_amount" class="form-label fw-semibold text-dark">Jumlah Koin (KP)</label>
                                    <input type="number" class="form-control rounded-3" id="coin_amount" name="coin_amount" required min="1" placeholder="Contoh: 100">
                                    <div class="form-text text-muted text-xs">Jumlah poin/koin Kuansing yang akan didapat player.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label fw-semibold text-dark">Harga (Rupiah)</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-3 bg-light text-dark">Rp</span>
                                        <input type="number" class="form-control rounded-end-3" id="price" name="price" required min="0" placeholder="Contoh: 10000">
                                    </div>
                                    <div class="form-text text-muted text-xs">Harga beli paket koin dalam mata uang rupiah (IDR).</div>
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold text-dark">Keterangan / Deskripsi</label>
                                    <textarea class="form-control rounded-3" id="description" name="description" rows="3" placeholder="Contoh: Paket Hemat 100 Koin"></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">
                                        <i class="ti ti-plus me-1"></i> Simpan Paket
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Packages List -->
                <div class="col-md-8 col-sm-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 text-dark fw-bold">Daftar Paket Aktif</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light text-muted uppercase font-semibold text-xs">
                                        <tr>
                                            <th>#</th>
                                            <th>JUMLAH KOIN</th>
                                            <th>HARGA RUPIAH</th>
                                            <th>DESKRIPSI</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($packages as $index => $pkg)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-light-warning text-warning border border-warning rounded-pill py-1 px-3 fw-bold">
                                                        <i class="ti ti-coin me-1"></i>{{ number_format($pkg->coin_amount) }} KP
                                                    </span>
                                                </td>
                                                <td class="fw-semibold text-dark">Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
                                                <td>{{ $pkg->description ?? '-' }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                        <!-- Edit Trigger Button -->
                                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $pkg->id }}">
                                                            <i class="ti ti-edit"></i> Edit
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <form action="{{ route('superadmin.packages.delete', $pkg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket koin ini?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                                <i class="ti ti-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </div>

                                                    <!-- Edit Modal -->
                                                    <div class="modal fade text-start" id="editModal{{ $pkg->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $pkg->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content rounded-4 border-0">
                                                                <div class="modal-header border-0 pb-0">
                                                                    <h5 class="modal-title fw-bold text-dark" id="editModalLabel{{ $pkg->id }}">Edit Paket Koin</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body p-4">
                                                                    <form action="{{ route('superadmin.packages.update', $pkg->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="mb-3">
                                                                            <label for="coin_amount{{ $pkg->id }}" class="form-label fw-semibold text-dark">Jumlah Koin (KP)</label>
                                                                            <input type="number" class="form-control rounded-3" id="coin_amount{{ $pkg->id }}" name="coin_amount" value="{{ $pkg->coin_amount }}" required min="1">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="price{{ $pkg->id }}" class="form-label fw-semibold text-dark">Harga (Rupiah)</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-text rounded-start-3 bg-light text-dark">Rp</span>
                                                                                <input type="number" class="form-control rounded-end-3" id="price{{ $pkg->id }}" name="price" value="{{ intval($pkg->price) }}" required min="0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="mb-4">
                                                                            <label for="description{{ $pkg->id }}" class="form-label fw-semibold text-dark">Keterangan / Deskripsi</label>
                                                                            <textarea class="form-control rounded-3" id="description{{ $pkg->id }}" name="description" rows="3">{{ $pkg->description }}</textarea>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">Simpan Perubahan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Belum ada paket koin yang dibuat.</td>
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
