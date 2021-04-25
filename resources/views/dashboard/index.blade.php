@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('page_content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <span class="block text-2xl">You're logged in!</span>

                    <span class="block mt-4">Click <a href="{{ route('dashboard.graph.example.is-tdm-allowed') }}" class="text-blue-600">here</a> to download the default graph to help answering the question 'Is TDM allowed?'. This graph can subsequently be imported on the 'Graphs' page.</span>
                </div>
            </div>
        </div>
    </div>
    <style>
        ul.list-inside li {
            margin-left: 1rem;
        }
    </style>
@endsection
