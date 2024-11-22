@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1>Add User</h1>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Address:</label>
            <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Contact:</label>
            <input type="text" id="contact" name="contact" value="{{ old('contact') }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Add User</button>
    </form>
@endsection
