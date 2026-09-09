@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">

            {{-- Header --}}
            <div class="text-center mb-6">

                <div class="mx-auto w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-11.31 2.65L3 16.34V20h3.66l1.5-1.5H11v-3h3v-2.34A6 6 0 0015 9z" />
                    </svg>
                </div>

                <h1 class="text-xl font-semibold text-gray-800">
                    Lupa Password?
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Masukkan email akun kamu untuk mendapatkan
                    link reset password.
                </p>

            </div>


            {{-- Success --}}
            @if (session('status'))

                <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('status') }}
                </div>

            @endif


            {{-- Error --}}
            @if ($errors->any())

                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>

            @endif


            <form
                action="{{ route('password.email') }}"
                method="POST"
            >

                @csrf

                <div class="mb-5">

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
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="contoh@email.com"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    >

                </div>


                <button
                    type="submit"
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg px-4 py-2.5 text-sm transition"
                >
                    Kirim Link Reset Password
                </button>

            </form>


            <div class="mt-5 text-center">

                <a
                    href="{{ route('login') }}"
                    class="text-sm text-gray-500 hover:text-amber-600"
                >
                    ← Kembali ke Login
                </a>

            </div>

        </div>

    </div>

</div>

@endsection