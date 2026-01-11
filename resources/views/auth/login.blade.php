<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orbit - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .input-field:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .logo-icon {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .feature-icon {
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: translateY(-5px) scale(1.1);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 overflow-hidden relative">
    <!-- Floating Shapes -->
    <div class="floating-shape w-64 h-64 top-20 -left-20" style="animation-delay: 0s;"></div>
    <div class="floating-shape w-48 h-48 top-40 right-10" style="animation-delay: 2s;"></div>
    <div class="floating-shape w-32 h-32 bottom-20 left-1/4" style="animation-delay: 4s;"></div>
    <div class="floating-shape w-40 h-40 bottom-10 right-1/4" style="animation-delay: 6s;"></div>

    <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
        <!-- Left Side - Branding -->
        <div class="text-white text-center lg:text-left space-y-8">
            <!-- Logo -->
            <div class="flex items-center justify-center lg:justify-start gap-4">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-2xl logo-icon">
                    <i class="fas fa-globe text-3xl text-purple-600"></i>
                </div>
                <h1 class="text-5xl font-bold tracking-tight">Orbit</h1>
            </div>

            <!-- Tagline -->
            <div class="space-y-4">
                <h2 class="text-3xl lg:text-4xl font-bold leading-tight">
                    Streamline Your<br>Workflow
                </h2>
                <p class="text-lg text-purple-100 max-w-md mx-auto lg:mx-0">
                    The ultimate platform for managing orders, tasks, teams, and portfolios. Everything you need, in perfect orbit.
                </p>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                    <p class="text-sm font-medium">Smart Tasks</p>
                </div>
                <div class="feature-card text-center p-4">
                    <div class="feature-icon w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <p class="text-sm font-medium">Team Sync</p>
                </div>
                <div class="feature-card text-center p-4">
                    <div class="feature-icon w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <p class="text-sm font-medium">Analytics</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="glass-card rounded-3xl shadow-2xl p-8 lg:p-10">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h3>
                <p class="text-gray-600">Sign in to continue to Orbit</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
                        class="input-field w-full px-4 py-3 rounded-xl outline-none text-gray-900 placeholder-gray-400"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="password">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="•••••••••"
                            class="input-field w-full px-4 py-3 rounded-xl outline-none text-gray-900 placeholder-gray-400 pr-12"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                        >
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors"
                        >
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="btn-primary w-full py-4 rounded-xl text-white font-semibold text-lg shadow-lg"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    Secure login powered by Orbit
                    <i class="fas fa-shield-alt ml-2 text-purple-600"></i>
                </p>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="absolute bottom-4 left-0 right-0 text-center z-10">
        <p class="text-white/70 text-sm">
            © 2025 Orbit. All rights reserved.
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
