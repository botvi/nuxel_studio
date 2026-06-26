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
                                <h4 class="m-0 text-dark fw-bold">Pengaturan API KlikQRIS</h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard-superadmin') }}">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">KlikQRIS Credentials</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 text-dark fw-bold">Konfigurasi Merchant</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('superadmin.settings.save') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="klikqris_merchant_id" class="form-label fw-semibold text-dark">ID Merchant KlikQRIS</label>
                                    <input type="text" class="form-control rounded-3" id="klikqris_merchant_id" name="klikqris_merchant_id" value="{{ $merchantId }}" required placeholder="Contoh: 178032012018">
                                    <div class="form-text text-muted text-xs">ID Merchant unik yang diperoleh dari dasbor merchant KlikQRIS.</div>
                                </div>

                                <div class="mb-4">
                                    <label for="klikqris_api_key" class="form-label fw-semibold text-dark">API Key KlikQRIS</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control rounded-start-3" id="klikqris_api_key" name="klikqris_api_key" value="{{ $apiKey }}" required placeholder="Masukkan API Key Anda">
                                        <button class="btn btn-outline-secondary rounded-end-3" type="button" id="btnToggleApiKey">
                                            <i class="ti ti-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text text-muted text-xs">Kunci otorisasi API rahasia untuk memvalidasi request pembayaran.</div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">
                                        <i class="ti ti-device-floppy me-1"></i> Simpan Konfigurasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-3"><i class="ti ti-info-circle me-1 text-primary"></i> Panduan Kredensial</h5>
                            <p class="text-muted text-sm mb-3">
                                Kredensial ini digunakan oleh backend game untuk berkomunikasi langsung dengan server gateway <strong>KlikQRIS</strong> saat player ingin melakukan pembelian koin (Kuansing Poin).
                            </p>
                            <h6 class="fw-bold text-dark text-xs text-uppercase mb-2">Langkah Sinkronisasi:</h6>
                            <ol class="text-muted text-sm ps-3 mb-4">
                                <li class="mb-2">Login ke dashboard KlikQRIS Anda.</li>
                                <li class="mb-2">Salin <strong>Merchant ID</strong> dan <strong>API Key</strong> dari menu integrasi pengembang.</li>
                                <li class="mb-2">Tempelkan data tersebut di form sebelah kiri dan simpan.</li>
                                <li class="mb-2">Pastikan webhook global di dasbor KlikQRIS diarahkan ke: <br>
                                    <code class="bg-white p-1 rounded border border-light text-xs font-mono select-all text-primary">{{ route('klikqris.webhook') }}</code>
                                </li>
                            </ol>
                            <div class="alert alert-warning border border-warning rounded-3 mb-0" role="alert">
                                <i class="ti ti-alert-triangle me-1"></i> <strong>Penting:</strong> Selalu amankan API Key Anda. Jangan membagikannya kepada siapa pun.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>

    @section('script')
        <script>
            document.getElementById('btnToggleApiKey').addEventListener('click', function () {
                var apiKeyInput = document.getElementById('klikqris_api_key');
                var toggleIcon = document.getElementById('toggleIcon');
                if (apiKeyInput.type === "password") {
                    apiKeyInput.type = "text";
                    toggleIcon.classList.remove('ti-eye');
                    toggleIcon.classList.add('ti-eye-off');
                } else {
                    apiKeyInput.type = "password";
                    toggleIcon.classList.remove('ti-eye-off');
                    toggleIcon.classList.add('ti-eye');
                }
            });
        </script>
    @endsection
@endsection
