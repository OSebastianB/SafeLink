@extends('layouts.admin')
@section('title', 'Tambah Reputasi')
@section('page_title', 'Tambah Data Reputasi')

@section('content')
<div class="card card-soft p-4 col-lg-8">
    <form method="POST" action="{{ route('admin.threats.store') }}">
        @csrf
        @include('admin.threats._form')
        <button class="btn btn-cyan">Simpan</button>
    </form>
</div>
@endsection
