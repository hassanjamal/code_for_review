@extends('layouts.tenant-admin.app')

@section('content')
    @component('components.dashboard-header')
        Properties
    @endcomponent<div class="px-12 py-8 mx-auto max-w-4xl">
        <div class="flex items-baseline justify-between">
            <div><h2 class="text-lg">
                    Add New Property
                </h2>
                <div class="mt-2 text-sm text-gray-700">
                    <div class="max-w-2xl">
                        Properties are groups of one or more offices or locations from your booking platform.
                        For example, MINDBODY groups locations under the broader entity that they call a Site.
                        Each site has a Site ID. Use this site ID to connect QuickerNotes to the provider.
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="px-6 py-12 bg-white overflow-hidden shadow-md rounded-lg text-gray-600">
                <div class="flex items-center justify-center">
                    <form class="w-full" method="POST" action="{{route('mindbody.properties.store')}}">
                        @csrf
                        @if($errors->any())
                            <div class="flex justify-end mb-4 text-red-600">
                                <ul>
                                    @foreach($errors->all() as $e)
                                        <li>* {{$e}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 font-medium md:text-right mb-1 md:mb-0 pr-4" for="name">
                                    Property Name
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" name="name" id="name" type="text" value="{{old('name')}}" placeholder="Property name">
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 font-medium md:text-right mb-1 md:mb-0 pr-4" for="site_id">
                                    Site ID
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" name="site_id" id="site_id" type="text" value="{{old('site_id')}}" placeholder="MINDBODY Site ID">
                            </div>
                        </div>

                        <div class="md:flex md:justify-end mb-6">
                            <button class="btn btn-sm btn-white" type="submit">Add Property</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
