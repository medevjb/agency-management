<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-yellow-50 via-white to-red-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="text-center">
            <!-- 403 Animation -->
            <div class="mb-8 relative">
                <div class="text-[180px] font-bold bg-gradient-to-r from-yellow-600 to-red-600 bg-clip-text text-transparent leading-none">
                    403
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-lock text-6xl text-yellow-200 opacity-50 animate-pulse"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Access Denied
            </h1>

            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Sorry, you don't have permission to access this page. Please contact your administrator if you believe this is an error.
            </p>

            <!-- Lock Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-yellow-100 rounded-full mb-4">
                    <i class="fas fa-ban text-4xl text-yellow-600"></i>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-yellow-600 to-red-600 hover:from-yellow-700 hover:to-red-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-home mr-2"></i>
                    Go Home
                </a>
                <button onclick="history.back()"
                        class="inline-flex items-center justify-center px-8 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-md hover:shadow-lg border-2 border-gray-200 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Go Back
                </button>
            </div>

            <!-- Help Section -->
            <div class="mt-12 p-6 bg-white/50 backdrop-blur-sm rounded-2xl border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    <i class="fas fa-shield-alt text-yellow-600 mr-2"></i>
                    Why Am I Seeing This?
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    This could be because:
                </p>
                <ul class="text-sm text-gray-600 text-left space-y-2 max-w-md mx-auto">
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 mt-0.5 mr-2"></i>
                        <span>You don't have sufficient permissions</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 mt-0.5 mr-2"></i>
                        <span>The page requires authentication</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-500 mt-0.5 mr-2"></i>
                        <span>Your session may have expired</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="fixed top-10 left-10 w-20 h-20 bg-yellow-100 rounded-full opacity-20 animate-bounce"></div>
        <div class="fixed bottom-10 right-10 w-32 h-32 bg-red-100 rounded-full opacity-20 animate-bounce" style="animation-delay: 0.5s;"></div>
        <div class="fixed top-1/2 right-20 w-16 h-16 bg-orange-100 rounded-full opacity-20 animate-bounce" style="animation-delay: 1s;"></div>
    </div>
</body>
</html>
