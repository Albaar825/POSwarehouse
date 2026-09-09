@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">

            <div class="text-center mb-6">

                <div class="mx-auto w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-4">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z"
                        />

                    </svg>

                </div>

                <h1 class="text-xl font-semibold text-gray-800">
                    Reset Password
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Buat password baru untuk akun kamu.
                </p>

            </div>


            @if ($errors->any())

                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">

                    {{ $errors->first() }}

                </div>

            @endif


            <form
                action="{{ route('password.update') }}"
                method="POST"
            >

                @csrf

                <input
                    type="hidden"
                    name="token"
                    value="{{ $token }}"
                >


                {{-- Email --}}
                <div class="mb-4">

                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        required
                        autocomplete="email"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    >

                </div>


                {{-- Password --}}
                <div class="mb-4">

                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                    >
                        Password Baru
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    >

                </div>


                {{-- Confirm Password --}}
                <div class="mb-5">

                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                    >
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi password baru"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    >

                </div>


                <button
                    type="submit"
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg px-4 py-2.5 text-sm transition"
                >
                    Reset Password
                </button>

            </form>

        </div>

    </div>

</div>

@endsection