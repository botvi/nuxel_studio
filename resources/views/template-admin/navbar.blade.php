<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard-superadmin') }}" class="b-brand text-primary">
                <img src="{{ asset('env') }}/logo_text.png" alt="Logo" style="height: 40px; object-fit: contain;">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin')
                    <li class="pc-item">
                        <a href="{{ route('dashboard-superadmin') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Manajemen User</label>
                        <i class="ti ti-users"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.users') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Daftar Player</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Manajemen Koin & Topup</label>
                        <i class="ti ti-wallet"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.packages') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-coin"></i></span>
                            <span class="pc-mtext">Paket Koin & Harga</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.transactions') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-receipt"></i></span>
                            <span class="pc-mtext">Riwayat Transaksi</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Pengaturan</label>
                        <i class="ti ti-settings"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('superadmin.settings') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-key"></i></span>
                            <span class="pc-mtext">KlikQRIS Credentials</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('main-menu') }}" class="pc-link" target="_blank">
                            <span class="pc-micon"><i class="ti ti-device-gamepad-2"></i></span>
                            <span class="pc-mtext">Masuk ke Game ↗</span>
                        </a>
                    </li>
                @elseif (Auth::user()->role == 'asisten')
                    <li class="pc-item">
                        <a href="/" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Data Panenpro</label>
                        <i class="ti ti-dashboard"></i>
                    </li>
                    <li class="pc-item">
                        <a href="" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user"></i></span>
                            <span class="pc-mtext">Data Elemen</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
