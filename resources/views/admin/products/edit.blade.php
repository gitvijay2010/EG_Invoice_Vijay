@extends('layouts.app')

@section('content')
    <h1>Edit Product</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required value="{{$product->name}}">
        </div>
        <div class="form-group">
            <label>Quantity:</label>
            <input type="number" name="quantity" min="01" class="form-control" required value="{{$product->quantity}}">
        </div>
        <div class="form-group">
            <label>Price:</label>
            <input type="number" step="0.01" min="01" name="price" class="form-control" required value="{{$product->price}}">
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" class="form-control">{{$product->description}}</textarea>
        </div>
        <div class="form-group">
            <label>Category:</label>
            <select name="category" class="form-control">
                @foreach(App\Models\Category::all() as $category)
                    <option @if($category->id == $product->category) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
@endsection
