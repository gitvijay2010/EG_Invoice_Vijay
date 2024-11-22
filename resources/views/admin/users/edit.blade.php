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
    <h1>Edit User</h1>

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required value="{{$user->name}}">
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="{{$user->email}}" required>
        </div>
        <div class="form-group">
            <label>Address:</label>
            <input type="text" id="address" name="address" value="{{$user->address}}" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Contact:</label>
            <input type="text" id="contact" name="contact" value="{{$user->contact}}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
@endsection
