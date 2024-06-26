<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'QuckerNotes') }}</title>
    
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body class="font-sans" style="">

<div class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex bg-gray-200">
        @include('layouts.tenant-admin._sidebar')
        <div class="flex-grow flex flex-col">
            <div class="relative shadow-md bg-white flex-shrink-0">
                <div class="flex justify-between items-center h-16 px-12">
                    {{-- Search Bar --}}
                    <div class="relative w-64">
                        <div class="relative z-50">
                            <input type="text" class="block w-full py-2 pl-12 pr-4 bg-gray-200 rounded-full border border-transparent focus:bg-white focus:border-gray-300 focus:outline-none">
                            <div class="flex items-center absolute left-0 inset-y-0 pl-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6 fill-current text-gray-600">
                                    <path d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    {{-- Account Dropdown --}}
                    <div class="flex items-center">
                        <a href="#" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6">
                                <path d="M12 21a2 2 0 0 1-1.41-.59l-.83-.82A2 2 0 0 0 8.34 19H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4a5 5 0 0 1 4 2v16z" class="fill-current text-gray-400"></path>
                                <path d="M12 21V5a5 5 0 0 1 4-2h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4.34a2 2 0 0 0-1.42.59l-.83.82A2 2 0 0 1 12 21z" class="fill-current text-gray-600"></path>
                            </svg>
                        </a>
                        <div class="ml-6">
                            <div class="relative">
                                <button type="button" class="block w-full focus:outline-none"><span class="flex items-center"><img
                                            src="https://www.gravatar.com/avatar/6943bae3c84139439748c6b67fa47710?d=https%3A%2F%2Fui-avatars.com%2Fapi%2FDustin%2BM%2BFraker"
                                            class="h-8 w-8 rounded-full"> <span class="ml-3">{{auth()->user()->full_name}}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 ml-2 text-gray-600">
                                            <path d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"></path>
                                        </svg>
                                        </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex-grow flex flex-col">
                <div class="flex-grow">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <div class="vue-portal-target"></div>
</div>

<!-- Scripts -->
<script src="{{ mix('js/app.js') }}"></script>
@yield('post_script')
</body>

</html>
