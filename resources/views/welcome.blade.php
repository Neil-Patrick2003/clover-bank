<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-neutral-50">
<!-- Navigation -->
<nav id="clover-nav" class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-emerald-100">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">Clover Bank</span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Features</a>
                <a href="#services" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Services</a>
                <a href="#security" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Security</a>
                <a href="#download" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Download</a>
                <button class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-lg font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg shadow-emerald-500/30">
                    Get Started
                </button>
            </div>

            <button class="md:hidden">
                <svg class="w-6 h-6 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="clover-hero" class="relative pt-32 pb-20 px-6 overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-emerald-50">
    <div class="absolute top-20 right-10 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
    <div class="absolute bottom-20 left-10 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 1s;"></div>
    <div class="max-w-7xl mx-auto relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="animate-slide-in-left">
                <div class="inline-block px-4 py-2 bg-emerald-100 rounded-full mb-6">
                    <span class="text-emerald-700 font-semibold text-sm">🍀 Your Money, Simplified</span>
                </div>
                <h1 class="text-5xl lg:text-6xl font-bold text-neutral-900 mb-6 leading-tight">
                    Banking That
                    <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent"> Grows </span>
                    With You
                </h1>
                <p class="text-xl text-neutral-600 mb-8 leading-relaxed">
                    Experience the future of mobile banking with Clover. Instant transfers, zero fees, and powerful financial tools at your fingertips.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="px-8 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-xl shadow-emerald-500/40 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                        </svg>
                        Download for iOS
                    </button>
                    <button class="px-8 py-4 bg-white text-emerald-700 border-2 border-emerald-600 rounded-xl font-semibold hover:bg-emerald-50 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                        </svg>
                        Download for Android
                    </button>
                </div>
                <div class="mt-8 flex items-center gap-6">
                    <div class="flex -space-x-3">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&search_term=woman,portrait,professional,smiling" alt="User" class="w-10 h-10 rounded-full border-2 border-white">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&search_term=man,portrait,professional,business" alt="User" class="w-10 h-10 rounded-full border-2 border-white">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&search_term=woman,business,professional,portrait" alt="User" class="w-10 h-10 rounded-full border-2 border-white">
                    </div>
                    <div>
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        <p class="text-sm text-neutral-600 mt-1"><span class="font-semibold text-neutral-900">50,000+</span> happy users</p>
                    </div>
                </div>
            </div>

            <div class="relative animate-slide-in-right">
                <div class="relative z-10">
                    <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&h=800&fit=crop&search_term=smartphone,mobile,banking,app,screen" alt="Clover Bank App" class="rounded-3xl shadow-2xl">
                </div>
                <div class="absolute -top-6 -right-6 w-72 h-72 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full opacity-20 blur-3xl"></div>
                <div class="absolute -bottom-6 -left-6 w-64 h-64 bg-gradient-to-tr from-emerald-300 to-emerald-500 rounded-full opacity-20 blur-3xl"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="clover-features" class="py-20 px-6 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <div class="inline-block px-4 py-2 bg-emerald-100 rounded-full mb-4">
                <span class="text-emerald-700 font-semibold text-sm">✨ Features</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 mb-4">
                Everything You Need,
                <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent"> Nothing You Don't</span>
            </h2>
            <p class="text-xl text-neutral-600 max-w-3xl mx-auto">
                Powerful features designed to make your financial life easier and more rewarding
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Instant Transfers</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Send money to anyone, anywhere in seconds. No waiting, no hassle, just instant transfers.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Bank-Level Security</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Your money is protected with military-grade encryption and multi-factor authentication.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.2s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Smart Analytics</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Track your spending patterns and get personalized insights to help you save more.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.3s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Zero Hidden Fees</h3>
                <p class="text-neutral-600 leading-relaxed">
                    No monthly fees, no transfer fees, no minimum balance. Banking should be free.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.4s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Virtual Cards</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Create instant virtual cards for online shopping with custom spending limits.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.5s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Savings Goals</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Set goals, track progress, and reach your financial dreams faster with smart automation.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="clover-services" class="py-20 px-6 bg-gradient-to-br from-emerald-50 to-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
            <div class="animate-on-scroll">
                <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=700&h=600&fit=crop&search_term=person,phone,payment,technology,mobile" alt="Easy Payments" class="rounded-2xl shadow-xl">
            </div>
            <div class="animate-on-scroll">
                <h3 class="text-3xl lg:text-4xl font-bold text-neutral-900 mb-6">
                    Pay Anyone, Anywhere,
                    <span class="text-emerald-600"> Anytime</span>
                </h3>
                <p class="text-lg text-neutral-600 mb-6 leading-relaxed">
                    Send money to friends, family, or businesses with just a phone number or email. No account numbers needed.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-neutral-700">Split bills with friends instantly</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-neutral-700">Schedule recurring payments</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-neutral-700">Request money with custom messages</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1 animate-on-scroll">
                <h3 class="text-3xl lg:text-4xl font-bold text-neutral-900 mb-6">
                    Insights That
                    <span class="text-emerald-600"> Empower You</span>
                </h3>
                <p class="text-lg text-neutral-600 mb-6 leading-relaxed">
                    Get a complete view of your financial health with intelligent categorization and personalized recommendations.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-neutral-700">Automatic expense tracking</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-neutral-700">Smart budget recommendations</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-neutral-700">Monthly spending reports</span>
                    </li>
                </ul>
            </div>
            <div class="order-1 lg:order-2 animate-on-scroll">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=700&h=600&fit=crop&search_term=analytics,dashboard,charts,data,graphs" alt="Analytics Dashboard" class="rounded-2xl shadow-xl">
            </div>
        </div>
    </div>
</section>

<!-- Security Section -->
<section id="clover-security" class="py-20 px-6 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <div class="inline-block px-4 py-2 bg-emerald-100 rounded-full mb-4">
                <span class="text-emerald-700 font-semibold text-sm">🔒 Security First</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 mb-4">
                Your Money is
                <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent"> Safe With Us</span>
            </h2>
            <p class="text-xl text-neutral-600 max-w-3xl mx-auto">
                We use industry-leading security measures to protect your money and personal information
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">256-Bit Encryption</h3>
                <p class="text-neutral-600 leading-relaxed">
                    All your data is protected with military-grade encryption, the same security used by major financial institutions worldwide.
                </p>
            </div>

            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">Two-Factor Authentication</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Add an extra layer of protection with biometric authentication and multi-factor verification for all transactions.
                </p>
            </div>

            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">24/7 Fraud Monitoring</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Our AI-powered system monitors your account around the clock to detect and prevent suspicious activity instantly.
                </p>
            </div>

            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll" style="animation-delay: 0.3s;">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">FDIC Insured</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Your deposits are FDIC insured up to $250,000, giving you peace of mind that your money is protected.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="clover-cta" class="py-20 px-6 bg-gradient-to-br from-emerald-600 to-emerald-800">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 animate-on-scroll">
            Ready to Take Control of Your Money?
        </h2>
        <p class="text-xl text-emerald-100 mb-10 animate-on-scroll">
            Join thousands of users who are already banking smarter with Clover
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-on-scroll">
            <button class="px-10 py-4 bg-white text-emerald-700 rounded-xl font-semibold hover:bg-emerald-50 transition-all shadow-xl flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                </svg>
                Download for iOS
            </button>
            <button class="px-10 py-4 bg-emerald-900 text-white rounded-xl font-semibold hover:bg-emerald-950 transition-all shadow-xl flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                </svg>
                Download for Android
            </button>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="clover-footer" class="bg-neutral-900 text-white py-12 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold">Clover Bank</span>
                </div>
                <p class="text-neutral-400">
                    Banking that grows with you. Simple, secure, and free.
                </p>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Product</h4>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-neutral-400 hover:text-emerald-400 transition-colors">Features</a></li>
                    <li><a href="#security" class="text-neutral-400 hover:text-emerald-400 transition-colors">Security</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Pricing</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Business</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Company</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">About Us</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Careers</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Press</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Privacy</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Terms</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Licenses</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Compliance</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-neutral-800 pt-6 mt-6">
            <div class="text-center text-neutral-500 text-sm">
                AI vibe coded development by <a href="https://biela.dev/" target="_blank" class="text-emerald-400 hover:text-emerald-300 transition-colors">Biela.dev</a>, powered by <a href="https://teachmecode.ae/" target="_blank" class="text-emerald-400 hover:text-emerald-300 transition-colors">TeachMeCode® Institute</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
