@extends('layouts.tenant-admin.app')

@section('content')
    @component('components.dashboard-header')
        Properties
    @endcomponent
    <div class="px-12 py-8 mx-auto max-w-4xl">
        <div class="flex items-baseline justify-between">
            <div><h2 class="text-lg">
                    Properties
                </h2>
                <div class="mt-2 text-sm text-gray-700">
                    <div class="max-w-2xl">
                        Properties are groups of one or more offices or locations from your booking platform.
                        For example, MINDBODY groups locations under the broader entity that they call a Site.
                        Each site has a Site ID. Use this site ID to connect QuickerNotes to the provider.
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 ml-4">
                <a href="{{route('properties.create')}}"
                   class="btn btn-sm btn-white transition-all">
                    Add Property
                </a>
            </div>
        </div>
        <div class="mt-6">
            @if($properties->isEmpty())
                <div class="px-6 py-12 bg-white overflow-hidden shadow-md rounded-lg text-gray-600">

                    <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-16 w-16">
                            <path d="M6 2h6v6c0 1.1.9 2 2 2h6v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2z"
                                  class="fill-current text-gray-300"></path>
                            <polygon points="14 2 20 8 14 8" class="fill-current text-gray-500"></polygon>
                        </svg>
                    </div>
                    <div class="mt-4 text-center max-w-xl mx-auto">
                        This organization does not have any properties.
                    </div>
                </div>
            @else
                @foreach($properties as $p)
                    <div
                        class="px-6 py-3 mt-4 bg-white overflow-hidden shadow-md rounded-lg text-gray-800 flex justify-between">
                        <div class="flex flex-1 justify-between items-start">
                            <div>
                                <p>{{$p->name}}</p>
                                <p class="text-gray-600 text-xs font-light mt-2">
                                    <a href="{{route('properties.show', [$p])}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 text-{{$p->verified ? 'green' : 'red'}}-700 fill-current inline-block">
                                            <circle cx="12" cy="12" r="10" class="text-{{$p->verified ? 'green' : 'red'}}-200 fill-current"/>
                                            <path class="secondary" d="M10 14.59l6.3-6.3a1 1 0 0 1 1.4 1.42l-7 7a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l2.3 2.3z"/>
                                        </svg>
                                        {{$p->verified ? 'connected' : 'setup connection'}} to API
                                    </a>
                                </p>
                            </div>
                            <div>{{$p->api_identifier}}</div>
                        </div>
                        <div class="">
                            <a href="{{routeForTenant('properties.show', [$p])}}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                     class="h-6 w-6 text-gray-600 fill-current ml-4">
                                    <path d="M9.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

