<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clover Bank - Secure and Simple Digital Banking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-neutral-50">
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
                <a href="#services" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Transfers & History</a>
                <a href="#security" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Security</a>
                <a href="#download" class="text-neutral-600 hover:text-emerald-600 transition-colors font-medium">Get Started</a>
                <button class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-lg font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg shadow-emerald-500/30">
                    Login
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
                    Banking That's
                    <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent"> Secure </span>
                    and Simple
                </h1>
                <p class="text-xl text-neutral-600 mb-8 leading-relaxed">
                    Manage your finances effortlessly with Clover. <span class="font-bold">Secure registration</span>, <span class="font-bold">instant transfers</span>, and clear <span class="font-bold">account oversight</span>.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="px-8 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-xl shadow-emerald-500/40 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15.01l-4.5-4.5 1.41-1.41L11 15.19l7.09-7.09 1.41 1.41-8.5 8.5z"/>
                        </svg>
                        Get Started (Sign Up)
                    </button>
                    <button class="px-8 py-4 bg-white text-emerald-700 border-2 border-emerald-600 rounded-xl font-semibold hover:bg-emerald-50 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Secure Login
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
                    <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&h=800&fit=crop&search_term=smartphone,mobile,banking,app,screen,dashboard" alt="Clover Bank App" class="rounded-3xl shadow-2xl">
                </div>
                <div class="absolute -top-6 -right-6 w-72 h-72 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full opacity-20 blur-3xl"></div>
                <div class="absolute -bottom-6 -left-6 w-64 h-64 bg-gradient-to-tr from-emerald-300 to-emerald-500 rounded-full opacity-20 blur-3xl"></div>
            </div>
        </div>
    </div>
</section>

<section id="clover-features" class="py-20 px-6 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <div class="inline-block px-4 py-2 bg-emerald-100 rounded-full mb-4">
                <span class="text-emerald-700 font-semibold text-sm">✨ Core Features</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 mb-4">
                Your Essential Banking,
                <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent"> Secured and Simplified</span>
            </h2>
            <p class="text-xl text-neutral-600 max-w-3xl mx-auto">
                All the tools you need for modern, day-to-day financial management.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3M6 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM6 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10.5 4a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Secure Account Access</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Easy and <span class="font-bold">secure user registration and login</span> to protect your financial data from the start.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Real-Time Balance View</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Instantly check your <span class="font-bold">account balance</span> and understand your current financial standing at a glance.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.2s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Instant Fund Transfers</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Easily manage your money with fast and reliable <span class="font-bold">fund transfers and transaction processing</span>.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.3s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Complete Transaction History</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Maintain full transparency with thorough <span class="font-bold">transaction history monitoring</span> and detailed reports.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.4s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Profile & Security Control</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Easily maintain your account details with full <span class="font-bold">profile management and password updates</span>.
                </p>
            </div>

            <div class="group p-8 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 hover:shadow-xl transition-all duration-300 animate-on-scroll" style="animation-delay: 0.5s;">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9.25 21M14.25 17L14.75 21M4 5h16M4 5l-.5 4h17l-.5-4M6 9l.75 3h10.5l.75-3M6 9h12M12 17c1.38 0 2.5-1.12 2.5-2.5S13.38 12 12 12s-2.5 1.12-2.5 2.5 1.12 2.5 2.5 2.5z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-3">Admin Dashboard</h3>
                <p class="text-neutral-600 leading-relaxed">
                    (For internal use) Dedicated <span class="font-bold">admin dashboard for user and system management</span> to ensure smooth service.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="clover-services" class="py-20 px-6 bg-gradient-to-br from-emerald-50 to-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
            <div class="animate-on-scroll">
                <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=700&h=600&fit=crop&search_term=mobile,banking,transfer,payment,screen" alt="Instant Transfers" class="rounded-2xl shadow-xl">
            </div>
            <div class="animate-on-scroll">
                <h3 class="text-3xl lg:text-4xl font-bold text-neutral-900 mb-6">
                    Effortless Money Movement and
                    <span class="text-emerald-600"> Real-Time Tracking</span>
                </h3>
                <p class="text-lg text-neutral-600 mb-6 leading-relaxed">
                    Move your funds instantly and monitor all your transactions from one clear dashboard. Our system is built for speed and security.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div class="text-neutral-700">
                            <span class="font-medium">Instant Fund Transfers:</span> Securely send and process transactions with immediate effect.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <div class="text-neutral-700">
                            <span class="font-medium">Full Transaction History:</span> Review and track every deposit, withdrawal, and transfer easily.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <div class="text-neutral-700">
                            <span class="font-medium">Clear Balance Viewing:</span> Always know your exact account status with a clear, updated balance display.
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1 animate-on-scroll">
                <h3 class="text-3xl lg:text-4xl font-bold text-neutral-900 mb-6">
                    Personalized Security and
                    <span class="text-emerald-600"> Profile Control</span>
                </h3>
                <p class="text-lg text-neutral-600 mb-6 leading-relaxed">
                    We empower you to manage your personal security settings and account information effortlessly. Your safety and control are our top priorities.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6z" />
                        </svg>
                        <div class="text-neutral-700">
                            <span class="font-medium">Secure Profile Updates:</span> Change your personal information and <span class="font-bold">update your password</span> anytime with ease.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <div class="text-neutral-700">
                            <span class="font-medium">Complete Profile Management:</span> Keep your details accurate with intuitive <span class="font-bold">profile management</span> tools.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <div class="text-neutral-700">
                            <span class="font-medium">Protected Access:</span> Guaranteed <span class="font-bold">secure user registration and login</span> protocols.
                        </div>
                    </li>
                </ul>
            </div>
            <div class="order-1 lg:order-2 animate-on-scroll">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=700&h=600&fit=crop&search_term=user,profile,settings,security,data" alt="Profile Management Screen" class="rounded-2xl shadow-xl">
            </div>
        </div>
    </div>
</section>

<section id="clover-security" class="py-20 px-6 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <div class="inline-block px-4 py-2 bg-emerald-100 rounded-full mb-4">
                <span class="text-emerald-700 font-semibold text-sm">🔒 Security & Compliance</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-neutral-900 mb-4">
                Protection from
                <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent"> Registration to Transfer</span>
            </h2>
            <p class="text-xl text-neutral-600 max-w-3xl mx-auto">
                We implement robust security standards to protect your transactions and personal data.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">Secure User Authentication</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Robust <span class="font-bold">secure user registration and login</span> protocols protect your account from unauthorized access.
                </p>
            </div>

            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll" style="animation-delay: 0.1s;">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">Encrypted Transaction Processing</h3>
                <p class="text-neutral-600 leading-relaxed">
                    All <span class="font-bold">fund transfers and transaction processing</span> are secured with industry-standard encryption.
                </p>
            </div>

            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">Continuous Monitoring</h3>
                <p class="text-neutral-600 leading-relaxed">
                    Our system provides <span class="font-bold">transaction history monitoring</span> to quickly spot and flag any suspicious activity.
                </p>
            </div>

            <div class="p-8 bg-gradient-to-br from-neutral-50 to-white rounded-2xl border border-neutral-200 animate-on-scroll" style="animation-delay: 0.3s;">
                <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-4">Profile & Password Protection</h3>
                <p class="text-neutral-600 leading-relaxed">
                    We enforce strong security policies for <span class="font-bold">password updates</span> as part of our <span class="font-bold">profile management</span> system.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="clover-cta" class="py-20 px-6 bg-gradient-to-br from-emerald-600 to-emerald-800">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 animate-on-scroll">
            Ready for Simple and Secure Banking?
        </h2>
        <p class="text-xl text-emerald-100 mb-10 animate-on-scroll">
            Sign up today to manage your money with confidence and ease.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-on-scroll">
            <button class="px-10 py-4 bg-white text-emerald-700 rounded-xl font-semibold hover:bg-emerald-50 transition-all shadow-xl flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15.01l-4.5-4.5 1.41-1.41L11 15.19l7.09-7.09 1.41 1.41-8.5 8.5z"/>
                </svg>
                Sign Up Now
            </button>
            <button class="px-10 py-4 bg-emerald-900 text-white rounded-xl font-semibold hover:bg-emerald-950 transition-all shadow-xl flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                User Login
            </button>
        </div>
    </div>
</section>

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
                    Your platform for <span class="font-bold">secure user registration, instant transfers</span>, and clear <span class="font-bold">account balance viewing</span>.
                </p>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Account</h4>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-neutral-400 hover:text-emerald-400 transition-colors">Core Features</a></li>
                    <li><a href="#services" class="text-neutral-400 hover:text-emerald-400 transition-colors">Transfers & History</a></li>
                    <li><a href="#security" class="text-neutral-400 hover:text-emerald-400 transition-colors">Security</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">My Profile</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Company</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">About Us</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Careers</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Admin Access</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Privacy</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Terms</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">Compliance</a></li>
                    <li><a href="#" class="text-neutral-400 hover:text-emerald-400 transition-colors">FDIC Insured</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
