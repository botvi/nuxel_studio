<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard-superadmin') }}" class="b-brand text-primary">
                <img src="{{ asset('env') }}/logo_text.png" alt="Logo" style="height: 40px;">
            </a>
        </div>
        @if (Auth::user()->role == 'asisten')
            <div class="navbar-content">
                <ul class="pc-navbar">
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
                </ul>
            </div>
        @endif
    </div>
</nav>
