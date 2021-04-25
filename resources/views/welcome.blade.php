@extends('layouts.guest')

@section('page_content')
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        @if (Route::has('login'))
            <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="text-center">
            <span class="block text-4xl mb-2 font-thin">Welcome to</span>
            {{-- <span class="block text-6xl font-bold">[ insert innovative and intuitive name here ]</span> --}}
            <span class="block text-6xl font-bold">Design Research</span>
        </div>
    </div>
@endsection
