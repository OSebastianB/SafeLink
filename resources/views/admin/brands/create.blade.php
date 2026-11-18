@extends('layouts.admin')
@section('title', 'Tambah Brand')
@section('page_title', 'Tambah Brand Watchlist')
@section('content')
<div class="card card-soft p-4 col-lg-8">
    <form method="POST" action="{{ route('admin.brands.store') }}">
        @csrf
        @include('admin.brands._form')
        <button class="btn btn-cyan">Simpan</button>
    </form>
</div>
@endsection
