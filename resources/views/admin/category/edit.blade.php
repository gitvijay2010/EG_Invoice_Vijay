@extends('layouts.app')

@section('content')
    <h1>Add Category</h1>

    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required value="{{$category->name}}">
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" class="form-control">{{$category->description}}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
@endsection
