@extends('layouts.tenant-admin.app')

@section('content')
    @component('components.dashboard-header')
        <p><a class="mr-1" href="{{routeForTenant('properties.index')}}">Properties</a> / {{ $property->name }} {{$property->api_identifier}}</p>
        @slot('right')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6 text-{{$property->verified ? 'green' : 'red'}}-700 fill-current inline-block">
                <circle cx="12" cy="12" r="10" class="text-{{$property->verified ? 'green' : 'red'}}-200 fill-current"/>
                <path class="secondary" d="M10 14.59l6.3-6.3a1 1 0 0 1 1.4 1.42l-7 7a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l2.3 2.3z"/>
            </svg>
            {{$property->verified ? 'Connected' : 'setup connection'}} to API
        @endslot
    @endcomponent
    <div class="px-12 py-8">
        <div>
            <div class="flex items-baseline justify-between">
                <div>
                    <h2 class="text-lg">Locations</h2>
                </div>
            </div>
            <div class="mt-4">
                @if(! $property->verified)
                    <div
                        class="px-6 py-12 bg-white overflow-hidden shadow-md rounded-lg w-1/3 mx-auto">
                        
                        <div class="mb-4">
                            <h3 class="text-xl uppercase tracking-wide text-center mb-4">Setup your api connection</h3>
                            <p>
                                In order for QuickerNotes to integrate with your API you will need to allow QuickerNotes
                                access to it. This can be done using the activation link or manually by entering the code
                                yourself.
                            </p>
                        </div>
                        
                        <div class="mb-8 rounded border border-gray-400 p-6">
                            <h4 class="text-lg mb-2 font-bold text-center">Automatic Activation</h4>
                            <ol class="list-decimal list-outside ml-5">
                                <li class="mb-3">Go to your mindbody site for this property by clicking the activate now
                                    button below.
                                </li>
                                <li class="mb-3"><span class="text-red-500 font-bold">* Important</span> Log in with
                                    your
                                    MINDBODY owner account when asked. You may have to logout and click this link again
                                    if
                                    you are already logged in as a non-owner user.
                                </li>
                                <li class="mb-3">The link should take you to a page and automatically add the
                                    integration
                                    code.
                                </li>
                                <li class="mb-3">Once you have completed the first 3 steps click the check integration
                                    button.
                                </li>
                            </ol>
                            <div class="flex justify-center mt-6">
                                <a class="btn btn-sm btn-black block w-3/4 text-center"
                                   href="{{$property->getMeta('link')}}" target="_blank">Activate Now</a>
                            </div>
                        </div>
                        
                        <div class="mb-4 rounded border border-gray-400 p-6">
                            <h4 class="text-lg mb-2 font-bold text-center">Manual Activation</h4>
                            <ol class="list-decimal list-outside ml-5">
                                <li class="mb-3"><span class="text-red-500 font-bold">* Important</span> Log into
                                    MINDBODY site with your owner account.
                                </li>
                                <li class="mb-3">From the navigation bar choose <span
                                        class="font-medium text-gray-900">Manager Tools</span>
                                    then
                                    <span class="font-medium text-gray-900">API
                                    Integrations</span>.
                                </li>
                                <li class="mb-3">On the API Integrations page enter the code below into the box provided and
                                    click the submit button.
                                </li>
                                <li class="mb-3">Once you have completed this step you should see the text <span
                                        class="italic font-medium text-gray-900">Activation Successful</span> and <span
                                        class="italic font-medium text-gray-900">QuickernotesLLC</span> under the heading, <span
                                        class="font-medium text-gray-900">Who has access
                                        to your API?</span>
                                </li>
                            </ol>
                            <div class="flex justify-center mt-6 text-center">
                                <input class="w-full text-center border border-gray-500" type="text" value="{{$property->getMeta('code')}}">
                            </div>
                        </div>
    
                        <form  method="POST" action="{{routeForTenant('mindbody.verify-ownership')}}">
                            @csrf
                            <input type="hidden" name="api_identifier" value="{{$property->api_identifier}}">
                            <button class="btn btn-white block">
                                Check connection
                            </button>
                        </form>
                    </div>
                @else
                    <div class="px-6 py-12 bg-white overflow-hidden shadow-md rounded-lg text-gray-600">
                        @forelse($locations as $location)
                            <div
                                class="px-6 py-3 mb-10 last:mb-0 bg-white border border-gray-300 overflow-hidden shadow rounded-lg text-gray-800 flex justify-between">
                                <div class="flex flex-1 justify-between items-start">
                                    <div>
                                        <p>{{$location->name}}</p>
                                    </div>
                                </div>
                                <form action="{{routeForTenant('location-subscriptions.store')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="location_id" value="{{$location->id}}">
                                    <button class="btn btn-sm btn-gray" type="submit">Subscribe</button>
                                </form>
                            </div>
                        @empty
                            <div class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-16 w-16">
                                    <path d="M6 2h6v6c0 1.1.9 2 2 2h6v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2z"
                                          class="fill-current text-gray-300"></path>
                                    <polygon points="14 2 20 8 14 8" class="fill-current text-gray-500"></polygon>
                                </svg>
                            </div>
                            <div class="mt-4 text-center max-w-xl mx-auto">
                                This property does not have any locations.
                            </div>
                        @endforelse
                        @endif
                    </div>
                    
            
            </div>
        </div>
    </div>
@endsection
