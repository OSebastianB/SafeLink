@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th><th></th></tr></thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge text-bg-{{ $user->isAdmin() ? 'dark' : 'secondary' }}">{{ $user->role }}</span></td>
                    <td class="small">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
