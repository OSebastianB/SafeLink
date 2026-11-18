@extends('layouts.admin')
@section('title', 'Brand Watchlist')
@section('page_title', 'Brand Watchlist')

@section('content')
<div class="mb-3"><a href="{{ route('admin.brands.create') }}" class="btn btn-cyan btn-sm">Tambah Brand</a></div>
<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Brand</th><th>Domain resmi</th><th>Keywords</th><th>Aktif</th><th></th></tr></thead>
            <tbody>
            @foreach($brands as $brand)
                <tr>
                    <td>{{ $brand->brand_name }}</td>
                    <td>{{ $brand->official_domain }}</td>
                    <td class="small">{{ implode(', ', $brand->keywords ?? []) }}</td>
                    <td>{{ $brand->is_active ? 'Ya' : 'Tidak' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $brands->links() }}
</div>
@endsection
