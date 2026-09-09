@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
    <div class="max-w-md mx-auto">
        <h1 class="text-lg font-semibold mb-1">Ganti Password</h1>
        <p class="text-sm text-gray-500 mb-6">Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})</p>

        <form method="POST" action="{{ route('profile.password.update') }}" class="bg-white rounded-lg shadow p-5 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                <input type="password" name="current_password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('current_password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition">
                Simpan Password Baru
            </button>
        </form>
    </div>
@endsection
