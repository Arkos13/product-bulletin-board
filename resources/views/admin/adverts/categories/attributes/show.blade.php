@extends('layouts.app')
@section('content')
    @include('admin.adverts.categories._nav')
    <div class="d-flex flex-row mb-3">
        <a href="{{ route('admin.adverts.categories.attributes.edit', [$category, $attribute]) }}" class="btn btn-primary mr-1">Edit</a>
        <form method="POST" action="{{ route('admin.adverts.categories.attributes.destroy', [$category, $attribute]) }}" class="mr-1">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th>Sort</th><td>{{ $attribute->sort }}</td>
        </tr>
        <tr>
            <th>Name</th><td>{{ $attribute->name }}</td>
        </tr>
        <tr>
            <th>Type</th><td>{{ $attribute->type }}</td>
        </tr>
        <tr>
            <th>Required</th><td>{{ $attribute->required ? 'Yes' : '' }}</td>
        </tr>
        </tbody>
    </table>
@endsection