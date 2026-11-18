@extends('layouts.admin')
@section('title', 'Detail Scan')
@section('page_title', 'Detail Pemeriksaan #'.$scan->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('scan.show', $scan) }}" class="btn btn-outline-secondary btn-sm">Buka tampilan publik</a>
</div>
<x-scan-result :scan="$scan" />
@endsection
