<div class="mdk-drawer js-mdk-drawer"
        id="default-drawer">
    <div class="mdk-drawer__content">
        <div class="sidebar sidebar-dark sidebar-left"
                data-perfect-scrollbar>

            <a href="{{url('admin/dashboard')}}"
                class="sidebar-brand" style="padding-top: 14px;">
                Admin Panel
            </a>

            <div class="sidebar-heading">Overview</div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <a class="sidebar-menu-button"
                        href="{{url('admin/dashboard')}}">
                        <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">insert_chart_outlined</span>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->is('admin/categories') ? 'active' : '' }}">
                    <a class="sidebar-menu-button"
                        href="{{url('admin/categories')}}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">account_box</i>
                        <span class="sidebar-menu-text">Categories</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->is('admin/products') ? 'active' : '' }}">
                    <a class="sidebar-menu-button"
                        href="{{url('admin/products')}}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">donut_large</i>
                        <span class="sidebar-menu-text">Products</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->is('admin/users') ? 'active' : '' }}">
                    <a class="sidebar-menu-button"
                        href="{{url('admin/users')}}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">people_outline</i>
                        <span class="sidebar-menu-text">Users</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- // END drawer -->