<!-- Header -->
<div class="navbar navbar-expand navbar-shadow px-0  pl-lg-16pt navbar-light bg-body"
        id="default-navbar"
        data-primary>

    <!-- Navbar toggler -->
    <button class="navbar-toggler d-block d-lg-none rounded-0"
            type="button"
            data-toggle="sidebar">
        <span class="material-icons">menu</span>
    </button>

    <!-- Navbar Brand -->
    
    <div class="flex"></div>

    <div class="nav navbar-nav flex-nowrap d-flex ml-0 mr-16pt">
        <div class="nav-item dropdown d-none d-sm-flex">
            <a href="#"
                class="nav-link d-flex align-items-center dropdown-toggle"
                data-toggle="dropdown">
                <span class="flex d-flex flex-column mr-8pt">
                    <!-- <span class="navbar-text-100">Laza Bogdan</span> -->
                    <small class="navbar-text-50">Administrator</small>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header"><strong>Account</strong></div>
                <a class="dropdown-item"
                    href="logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

    </div>

</div>

<!-- // END Header -->