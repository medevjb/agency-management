<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="text-center">
            <!-- 500 Animation -->
            <div class="mb-8 relative">
                <div class="text-[180px] font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent leading-none">
                    500
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-server text-6xl text-red-200 opacity-50 animate-pulse"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Server Error
            </h1>

            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Something went wrong on our end. Our team has been notified and we're working to fix it!
            </p>

            <!-- Error Illustration -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-home mr-2"></i>
                    Go Home
                </a>
                <a href="javascript:location.reload()"
                   class="inline-flex items-center justify-center px-8 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-md hover:shadow-lg border-2 border-gray-200 transition-all duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Try Again
                </a>
            </div>

            <!-- Help Section -->
            <div class="mt-12 p-6 bg-white/50 backdrop-blur-sm rounded-2xl border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    <i class="fas fa-tools text-orange-500 mr-2"></i>
                    What You Can Do
                </h3>
                <ul class="text-sm text-gray-600 text-left space-y-2 max-w-md mx-auto">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <span>Refresh the page or try again later</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <span>Check your internet connection</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <span>Contact support if the problem persists</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="fixed top-10 left-10 w-20 h-20 bg-red-100 rounded-full opacity-20 animate-bounce"></div>
        <div class="fixed bottom-10 right-10 w-32 h-32 bg-orange-100 rounded-full opacity-20 animate-bounce" style="animation-delay: 0.5s;"></div>
        <div class="fixed top-1/2 right-20 w-16 h-16 bg-yellow-100 rounded-full opacity-20 animate-bounce" style="animation-delay: 1s;"></div>
    </div>
</body>
</html>
