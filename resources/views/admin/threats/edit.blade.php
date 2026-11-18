@extends('layouts.admin')
@section('title', 'Edit Reputasi')
@section('page_title', 'Edit Data Reputasi')

@section('content')
<div class="card card-soft p-4 col-lg-8">
    <form method="POST" action="{{ route('admin.threats.update', $record) }}">
        @csrf @method('PUT')
        @include('admin.threats._form', ['record' => $record])
        <button class="btn btn-cyan">Perbarui</button>
    </form>
</div>
@endsection
