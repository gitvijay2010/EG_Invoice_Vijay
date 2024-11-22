<!-- resources/views/dashboard.blade.php -->

@extends('layouts.app') <!-- Extend the layout file -->

@section('content') <!-- This will replace @yield('content') in the layout -->
<style type="text/css">
.boxx{
    margin: auto;
    width: 60%;
    border: 3px solid #cccccc;
    padding: 10px;
    text-align: center;
}
</style>
<div class="page-separator">
    <div class="page-separator__text">Dashboard</div>
</div>

<div class="row card-group-row mb-lg-8pt">
    <div class="col-xl-3 col-md-6 card-group-row__col">
            <h1>Welcome</h1>
    </div>
</div>
<div class="row">
        <div class="col-xl-3 boxx"><a href="{{ url('admin/categories') }}">Categories</a></div>
        <div class="col-xl-3 boxx"><a href="{{ url('admin/products') }}">Products</a></div>
        <div class="col-xl-3 boxx"><a href="{{ url('admin/users') }}">Users</a></div>
</div>
@endsection <!-- End the content section -->
