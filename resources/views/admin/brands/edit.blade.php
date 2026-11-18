@extends('layouts.admin')
@section('title', 'Edit Brand')
@section('page_title', 'Edit Brand Watchlist')
@section('content')
<div class="card card-soft p-4 col-lg-8">
    <form method="POST" action="{{ route('admin.brands.update', $brand) }}">
        @csrf @method('PUT')
        @include('admin.brands._form', ['brand' => $brand])
        <button class="btn btn-cyan">Perbarui</button>
    </form>
</div>
@endsection
