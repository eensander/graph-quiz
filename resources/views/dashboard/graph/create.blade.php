@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-lg text-gray-800 leading-tight">
        {{ __('Create new graph') }}
    </h2>
@endsection

@section('page_content')
    <div class="py-6" id="app">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                @if ($errors->any())
                    <div class="w-full bg-red-400 rounded-t text-white p-6">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="p-6 bg-white border-b border-gray-200" method="post" action="{{ route('dashboard.graph.store') }}">
                    @csrf

                    <div class="w-1/2">
                        <label class="flex flex-col text-gray-800">
                            Graph name
                            <input name="name" type="text" class="mt-2 rounded" />
                        </label>
                        <input value="Create new graph" type="submit" class="cursor-pointer mt-3 focus:ring-2 focus:ring-offset-2 text-sm px-3 py-2 rounded-md font-semibold text-white bg-blue-500 hover:bg-blue-600" />
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection
