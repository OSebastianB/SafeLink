@extends('layouts.admin')
@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Pengguna')

@section('content')
<div class="card card-soft p-4 col-lg-7">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="user" @selected(old('role', $user->role)==='user')>user</option>
                <option value="admin" @selected(old('role', $user->role)==='admin')>admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Password baru (opsional)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <button class="btn btn-cyan">Simpan</button>
    </form>
</div>
@endsection
