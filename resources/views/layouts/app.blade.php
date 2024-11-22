<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
    @include('includes.head')

    <body class="layout-app layout-sticky-subnav">
        <div class="preloader">
            <div class="sk-chase">
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
            </div>
        </div>

        <div class="mdk-drawer-layout js-mdk-drawer-layout" data-push data-responsive-width="992px">
            <div class="mdk-drawer-layout__content page-content">
                @include('includes.header') <!-- Include header -->

                <div class="container-fluid page__container">
                    <div class="page-section">
                        @yield('content') <!-- This will hold the specific page content -->
                    </div>
                </div>
            </div>
            @include('includes.sidebar') <!-- Include sidebar -->
        </div>

        @include('includes.script') <!-- Include scripts -->
    </body>
</html>
