<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Kasir & Warehouse</title>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    {{-- Login CSS --}}
    @vite(['resources/css/login.css', 'resources/js/app.js'])
</head>

<body>

    {{-- Background Decoration --}}
    <div class="background-decoration">

        <div class="orange-circle orange-circle-one"></div>

        <div class="orange-circle orange-circle-two"></div>

        <div class="orange-glow"></div>

        <div class="dots dots-right"></div>

        <div class="dots dots-left"></div>

        <div class="wave wave-one"></div>
        <div class="wave wave-two"></div>

    </div>


    {{-- Main --}}
    <main class="login-page">

        <div class="login-container">

            {{-- Login Card --}}
            <div class="login-card">

                {{-- Logo --}}
                <div class="logo-wrapper">

                    <div class="logo-box">

                        <img
                            src="{{ asset('images/hwhouse.jpg') }}"
                            alt="HW Logo"
                            class="logo-image"
                        >

                    </div>

                </div>


                {{-- Header --}}
                <div class="login-header">

                    <h1>
                        Sistem
                        <span>Kasir & Warehouse</span>
                    </h1>

                    <p>
                        Silakan login untuk melanjutkan
                    </p>

                    <div class="orange-line"></div>

                </div>


                {{-- Error --}}
                @if ($errors->any())

                    <div class="error-message">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.74 3h15.58a2 2 0 001.74-3l-7.82-14a2 2 0 00-3.48 0z"
                            />
                        </svg>

                        <span>
                            {{ $errors->first() }}
                        </span>

                    </div>

                @endif


                {{-- Form --}}
                <form
                    method="POST"
                    action="{{ url('/login') }}"
                    class="login-form"
                >

                    @csrf


                    {{-- Email --}}
                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <div class="input-wrapper">

                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M3 8l9 6 9-6"
                                />

                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="14"
                                    rx="2"
                                    stroke-width="1.8"
                                />
                            </svg>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="Masukkan email Anda"
                            >

                        </div>

                    </div>


                    {{-- Password --}}
                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <div class="input-wrapper">

                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <rect
                                    x="4"
                                    y="10"
                                    width="16"
                                    height="11"
                                    rx="2"
                                    stroke-width="1.8"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-width="1.8"
                                    d="M8 10V7a4 4 0 018 0v3"
                                />
                            </svg>


                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password Anda"
                            >


                            {{-- Show Password --}}
                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword()"
                                aria-label="Tampilkan password"
                            >

                                <svg
                                    id="eyeIcon"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5"
                                        stroke-width="1.8"
                                    />
                                </svg>

                            </button>

                        </div>

                    </div>


                    {{-- Remember --}}
                    <div class="form-options">

                        <label class="remember-me">

                            <input
                                type="checkbox"
                                name="remember"
                            >

                            <span>
                                Ingat saya
                            </span>

                        </label>


                        <span class="forgot-password">
                            Lupa password?
                        </span>

                    </div>


                    {{-- Login Button --}}
                    <button
                        type="submit"
                        class="login-button"
                    >

                        <span>
                            Masuk
                        </span>

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                            />
                        </svg>

                    </button>

                </form>


                {{-- Divider --}}
                <div class="divider">

                    <span></span>

                    <p>atau</p>

                    <span></span>

                </div>


                {{-- Help --}}
                <div class="help-box">

                    <div class="help-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M4 14v-2a8 8 0 0116 0v2"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M4 14h2a1 1 0 011 1v3a1 1 0 01-1 1H5a2 2 0 01-2-2v-2a1 1 0 011-1zm16 0h-2a1 1 0 00-1 1v3a1 1 0 001 1h1a2 2 0 002-2v-2a1 1 0 00-1-1z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-width="1.8"
                                d="M17 19c-.8 1-2 1.5-4 1.5"
                            />

                        </svg>

                    </div>


                    <div class="help-content">

                        <h3>
                            Butuh bantuan?
                        </h3>

                        <p>
                            Hubungi admin jika lupa password
                            atau belum memiliki akun.
                        </p>

                    </div>

                </div>


                {{-- Footer --}}
                <div class="login-footer">

                    <p>
                        © {{ date('Y') }} Sistem Kasir & Warehouse
                    </p>

                    <span>
                        Secure Management System
                    </span>

                </div>

            </div>

        </div>

    </main>


    {{-- Password Toggle --}}
    <script>

        function togglePassword() {

            const password =
                document.getElementById('password');

            const eyeIcon =
                document.getElementById('eyeIcon');


            if (password.type === 'password') {

                password.type = 'text';

                eyeIcon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M3 3l18 18"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M10.6 10.6a2 2 0 002.8 2.8"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M9.9 5.2A9.7 9.7 0 0112 5c6 0 9.5 7 9.5 7a16.6 16.6 0 01-3.1 3.9"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M6.1 6.1C3.9 8 2.5 12 2.5 12s3.5 7 9.5 7c1.5 0 2.8-.4 4-.9"
                    />
                `;

            } else {

                password.type = 'password';

                eyeIcon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="2.5"
                        stroke-width="1.8"
                    />
                `;

            }

        }

    </script>

</body>
</html>
