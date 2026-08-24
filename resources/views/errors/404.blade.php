<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Keep this CDN Tailwind instance in sync with the app's brand palette
        // (tailwind.config.js) since it doesn't read that file.
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: { 50: '#FFF3EC', 100: '#FFE4D3', 200: '#FFC7A6', 300: '#FFA26E', 400: '#FF7A3D', 500: '#FF5A1F', 600: '#F0430A', 700: '#C63508', 800: '#9C2B0B', 900: '#7A250D', 950: '#431004' },
                        purple: { 50: '#FFF8EB', 100: '#FFEFC7', 200: '#FFDD8A', 300: '#FFC94D', 400: '#FFB238', 500: '#F59A0F', 600: '#D67D06', 700: '#AD5F08', 800: '#8A4A0E', 900: '#723D10', 950: '#5C3208' },
                        violet: { 50: '#FFF8EB', 100: '#FFEFC7', 200: '#FFDD8A', 300: '#FFC94D', 400: '#FFB238', 500: '#F59A0F', 600: '#D67D06', 700: '#AD5F08', 800: '#8A4A0E', 900: '#723D10', 950: '#5C3208' },
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="text-center">
            <!-- 404 Animation -->
            <div class="mb-8 relative">
                <div class="text-[180px] font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent leading-none">
                    404
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-search text-6xl text-indigo-200 opacity-50 animate-pulse"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Oops! Page Not Found
            </h1>

            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                The page you're looking for seems to have wandered off. Don't worry, let's get you back on track!
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
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
                    <i class="fas fa-question-circle text-indigo-500 mr-2"></i>
                    Need Help?
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Here are some helpful links to get you back on track:
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-folder mr-1"></i>Orders
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="{{ route('teams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-users mr-1"></i>Teams
                    </a>
                </div>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="fixed top-10 left-10 w-20 h-20 bg-indigo-100 rounded-full opacity-20 animate-bounce"></div>
        <div class="fixed bottom-10 right-10 w-32 h-32 bg-purple-100 rounded-full opacity-20 animate-bounce" style="animation-delay: 0.5s;"></div>
        <div class="fixed top-1/2 right-20 w-16 h-16 bg-pink-100 rounded-full opacity-20 animate-bounce" style="animation-delay: 1s;"></div>
    </div>
</body>
</html>
