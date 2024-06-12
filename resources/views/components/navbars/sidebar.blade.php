@props(['activePage'])

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-black opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('img/mlpLogo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-4 font-weight-bold" style="color: black;">MLP Asset Management</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ $activePage == 'dashboard' ? 'active bg-gradient-success' : '' }}" href="{{ route('dashboard') }}" style="color: {{ $activePage == 'dashboard' ? 'white' : 'black' }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10" style="color: {{ $activePage == 'dashboard' ? 'white' : 'black' }};">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activePage == 'inventory' ? 'active bg-gradient-success' : '' }}" href="{{ route('inventory') }}" style="color: {{ $activePage == 'inventory' ? 'white' : 'black' }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-plus-circle" style="color: {{ $activePage == 'inventory' ? 'white' : 'black' }};"></i>
                    </div>
                    <span class="nav-link-text ms-1">Input Asset</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activePage == 'repair_inventory' ? 'active bg-gradient-success' : '' }}" href="{{ route('repair_inventory') }}" style="color: {{ $activePage == 'repair_inventory' ? 'white' : 'black' }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-wrench" style="color: {{ $activePage == 'repair_inventory' ? 'white' : 'black' }};"></i>
                    </div>
                    <span class="nav-link-text ms-1">Repair Asset</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activePage == 'dispose_inventory' ? 'active bg-gradient-success' : '' }}" href="{{ route('dispose_inventory') }}" style="color: {{ $activePage == 'dispose_inventory' ? 'white' : 'black' }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-trash-alt" style="color: {{ $activePage == 'dispose_inventory' ? 'white' : 'black' }};"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dispose Asset</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activePage == 'history_inventory' ? 'active bg-gradient-success' : '' }}" href="{{ route('history_inventory') }}" style="color: {{ $activePage == 'history_inventory' ? 'white' : 'black' }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-history" style="color: {{ $activePage == 'history_inventory' ? 'white' : 'black' }};"></i>
                    </div>
                    <span class="nav-link-text ms-1">History Asset</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activePage == 'user-management' ? 'active bg-gradient-success' : '' }}" href="{{ route('user-management') }}" style="color: {{ $activePage == 'user-management' ? 'white' : 'black' }};">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-circle" style="color: {{ $activePage == 'user-management' ? 'white' : 'black' }};"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
            </li>
        </ul>
    </div>
</aside>