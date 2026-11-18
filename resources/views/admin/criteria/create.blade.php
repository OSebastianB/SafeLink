@extends('layouts.admin')
@section('title', 'Tambah Kriteria')
@section('page_title', 'Tambah Kriteria')

@section('content')
<div class="card card-soft p-4 col-lg-8">
    <form method="POST" action="{{ route('admin.criteria.store') }}">
        @csrf
        @include('admin.criteria._form')
        <button class="btn btn-cyan">Simpan</button>
        <a href="{{ route('admin.criteria.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div>
@endsection
