<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | Hệ Thống Quản Trị</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .admin-sidebar-brand-accent {
            color: #f6dfb0 !important;
        }

        .admin-sidebar-badge {
            color: #f0d49a !important;
        }

        .admin-sidebar-link {
            color: #f2e7d2 !important;
        }

        .admin-sidebar-link:hover {
            color: #fff8ec !important;
            background-color: #1f2937;
        }

        .admin-sidebar-link.is-active {
            color: #ffffff !important;
            background-color: #1f2937;
            border-right: 4px solid #f0d49a;
        }

        .admin-sidebar-footer-link {
            color: #e6d5b4 !important;
        }

        .admin-sidebar-footer-link:hover {
            color: #fff8ec !important;
        }

        .admin-toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            width: min(420px, calc(100vw - 2rem));
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.16);
            overflow: hidden;
        }

        .admin-toast.is-success {
            border-color: #bbf7d0;
        }

        .admin-toast.is-error {
            border-color: #fecaca;
        }

        .admin-toast__bar {
            height: 4px;
        }

        .admin-toast.is-success .admin-toast__bar {
            background: linear-gradient(90deg, #22c55e 0%, #86efac 100%);
        }

        .admin-toast.is-error .admin-toast__bar {
            background: linear-gradient(90deg, #ef4444 0%, #fca5a5 100%);
        }

        .admin-toast__body {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem;
        }

        .admin-toast__icon {
            flex-shrink: 0;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
        }

        .admin-toast.is-success .admin-toast__icon {
            background: #dcfce7;
            color: #15803d;
        }

        .admin-toast.is-error .admin-toast__icon {
            background: #fee2e2;
            color: #b91c1c;
        }

        .admin-toast__content {
            flex: 1;
            min-width: 0;
        }

        .admin-toast__title {
            margin-bottom: 0.2rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
        }

        .admin-toast__message {
            font-size: 0.92rem;
            line-height: 1.55;
            color: #374151;
        }

        .admin-toast__close {
            flex-shrink: 0;
            border: 0;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
            padding: 0.15rem;
        }

        .admin-toast__close:hover {
            color: #111827;
        }

        .admin-toast.is-hidden {
            display: none;
        }

        @media (max-width: 640px) {
            .admin-toast {
                top: 1rem;
                right: 1rem;
                left: 1rem;
                width: auto;
            }
        }
    </style>
    @if(request()->routeIs('admin.categories.edit', 'admin.categories.create'))
    <style>
        div:has(> label[for="sort_order"]),
        div:has(> #sort_order),
        label[for="sort_order"],
        #sort_order {
            display: none !important;
        }
    </style>
    @endif
    @if(request()->routeIs('admin.categories.index'))
    <style>
        table tbody tr {
            cursor: pointer;
        }

        table thead th:nth-child(6),
        table tbody td:nth-child(6),
        table tbody td:last-child a[href*="/edit"] {
            display: none !important;
        }
    </style>
    @endif
</head>
<body class="admin-mode bg-gray-100 font-[Inter] text-gray-800">
    @if(session('success') || session('error'))
        @php
            $toastType = session('error') ? 'error' : 'success';
            $toastTitle = session('error') ? 'Không thể thực hiện' : 'Thành công';
            $toastMessage = session('error') ?: session('success');
            $toastIcon = session('error') ? '!' : '✓';
        @endphp
        <div class="admin-toast is-{{ $toastType }}" data-admin-toast role="alert" aria-live="assertive">
            <div class="admin-toast__bar"></div>
            <div class="admin-toast__body">
                <span class="admin-toast__icon">{{ $toastIcon }}</span>
                <div class="admin-toast__content">
                    <div class="admin-toast__title">{{ $toastTitle }}</div>
                    <div class="admin-toast__message">{{ $toastMessage }}</div>
                </div>
                <button type="button" class="admin-toast__close" data-dismiss-admin-toast aria-label="Đóng thông báo">&times;</button>
            </div>
        </div>
    @endif

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 border-r border-gray-800 hidden md:flex flex-col flex-shrink-0">
            <div class="h-16 flex items-center justify-center border-b border-gray-800 px-4">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-gray-700 bg-gray-950 px-4 py-2 font-bold text-xl tracking-wider shadow-sm">
                    <span style="color: #f8fafc;">COOL</span><span class="admin-sidebar-brand-accent">WEAR</span>
                    <span class="admin-sidebar-badge rounded-full bg-gray-800 px-2 py-0.5 text-[10px] font-semibold tracking-[0.2em]">ADMIN</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Trang Chủ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Sản Phẩm
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h5l2 2h11v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                            </svg>
                            Danh Mục
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Đơn Hàng
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customers.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.customers.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Khách Hàng
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reviews.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.052 3.236a1 1 0 00.95.69h3.403c.969 0 1.371 1.24.588 1.81l-2.753 2a1 1 0 00-.364 1.118l1.052 3.237c.3.92-.755 1.688-1.538 1.118l-2.752-2a1 1 0 00-1.176 0l-2.752 2c-.784.57-1.838-.197-1.539-1.118l1.053-3.237a1 1 0 00-.364-1.118l-2.753-2c-.783-.57-.38-1.81.588-1.81h3.403a1 1 0 00.951-.69l1.051-3.236z"/>
                            </svg>
                            Đánh Giá
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.coupons.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7.5-3a4.5 4.5 0 100-9 4.5 4.5 0 000 9zm-5.5 2h7"/>
                            </svg>
                            Mã Giảm Giá
                        </a>
                    </li>
                    @if(auth()->user()->role === 'admin')
                    <li>
                        <a href="{{ route('admin.employees.index') }}" class="admin-sidebar-link flex items-center gap-3 px-6 py-3 font-medium transition-colors {{ request()->routeIs('admin.employees.*') ? 'is-active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Nhân Viên
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <a href="{{ route('home') }}" target="_blank" class="admin-sidebar-footer-link flex items-center gap-3 px-4 py-2 text-sm font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Xem Trang Khách
                </a>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            {{-- Top Navbar --}}
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center">
                    <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <!-- Tùy chọn Breadcrumbs -->
                    <span class="ml-4 text-gray-600 font-medium">@yield('page_title', 'Dashboard')</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium text-sm focus:outline-none">
                            <span class="bg-[#C5A572] text-white rounded-full w-8 h-8 flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->employee?->full_name ?? auth()->user()->customer?->full_name ?? auth()->user()->email ?? 'A', 0, 1)) }}
                            </span>
                            {{ auth()->user()->employee?->full_name ?? auth()->user()->customer?->full_name ?? auth()->user()->email }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <!-- Dropdown -->
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 uppercase tracking-wider font-medium">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @if(request()->routeIs('admin.categories.index'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('table tbody tr').forEach((row) => {
                const editLink = row.querySelector('a[href*="/admin/categories/"][href$="/edit"]');

                if (!editLink) {
                    return;
                }

                row.addEventListener('click', (event) => {
                    if (event.target.closest('form, button, a')) {
                        return;
                    }

                    window.location.href = editLink.href;
                });
            });
        });
    </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-dismiss-admin-toast]').forEach((button) => {
                button.addEventListener('click', () => {
                    const toast = button.closest('[data-admin-toast]');

                    if (!toast) {
                        return;
                    }

                    toast.classList.add('is-hidden');
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
