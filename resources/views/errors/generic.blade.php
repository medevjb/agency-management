<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-gray-600 to-blue-600 rounded-full shadow-2xl">
                    <i class="fas fa-exclamation-mark text-6xl text-white">!</i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Oops! Something Went Wrong
            </h1>

            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                {{ $message ?? 'An unexpected error occurred. Please try again later.' }}
            </p>

            @if(isset($exception) && config('app.debug'))
            <!-- Debug Information (Only in Development) -->
            <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-xl text-left">
                <h3 class="text-sm font-semibold text-red-800 mb-2">
                    <i class="fas fa-bug mr-1"></i>
                    Error Details
                </h3>
                <p class="text-xs text-red-700 font-mono break-all">
                    {{ $exception->getMessage() }}
                </p>
                <p class="text-xs text-red-600 mt-2 font-mono">
                    {{ $exception->getFile() }}:{{ $exception->getLine() }}
                </p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-gray-600 to-blue-600 hover:from-gray-700 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
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
                    <i class="fas fa-life-ring text-blue-500 mr-2"></i>
                    Need Assistance?
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    If this problem persists, please reach out to our support team:
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="mailto:support@example.com" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-envelope mr-2"></i>
                        support@example.com
                    </a>
                </div>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="fixed top-10 left-10 w-20 h-20 bg-gray-200 rounded-full opacity-20 animate-bounce"></div>
        <div class="fixed bottom-10 right-10 w-32 h-32 bg-blue-200 rounded-full opacity-20 animate-bounce" style="animation-delay: 0.5s;"></div>
        <div class="fixed top-1/2 right-20 w-16 h-16 bg-indigo-200 rounded-full opacity-20 animate-bounce" style="animation-delay: 1s;"></div>
    </div>
</body>
</html>
