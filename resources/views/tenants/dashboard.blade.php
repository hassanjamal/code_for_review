@extends('layouts.tenant-admin.app')

@section('content')
    @component('components.dashboard-header')
        Company Dashboard
    @endcomponent
    <div class="px-12 py-8 mx-auto max-w-4xl">
        <div>
            <div class="flex items-baseline justify-between">
                <div>
                    <h2 class="text-lg">
                        Welcome To QuickerNotes
                    </h2>
                </div>
                <!---->
            </div>
            <div class="mt-4">
                <div class="px-6 py-4 bg-white shadow-md rounded-lg">
                    <!---->
                    <div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6">
                                <path d="M8.23 10.77a7.01 7.01 0 1 1 5 5L11 18H9v2H7v2.03H2V17l6.23-6.23zM17 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" class="fill-current text-gray-400"></path>
                                <path d="M6.2 18.7a1 1 0 1 1-1.4-1.4l4-4a1 1 0 1 1 1.4 1.4l-4 4z" class="fill-current text-gray-600"></path>
                            </svg>
                            <div class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider">
                                <a href="{{routeForTenant('properties.create')}}" class="">
                                    Connect To A Booking Platform
                                </a>
                            </div>
                        </div>
                        <div class="mt-3 mb-8 max-w-2xl text-sm text-gray-700">
                            To get started with QuickerNotes, <a href="/app/team/settings/cloud-providers" class="font-bold underline hover:text-gray-900">you'll
                                need to link an scheduling provider</a>. Once connected, QuickerNotes will be able to add locations and allow your staff members to take notes and more.
                        </div>
                    </div>
                    <div class="mt-8 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6">
                            <path d="M12 21a2 2 0 0 1-1.41-.59l-.83-.82A2 2 0 0 0 8.34 19H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4a5 5 0 0 1 4 2v16z" class="fill-current text-gray-400"></path>
                            <path d="M12 21V5a5 5 0 0 1 4-2h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4.34a2 2 0 0 0-1.42.59l-.83.82A2 2 0 0 1 12 21z" class="fill-current text-gray-600"></path>
                        </svg>
                        <div class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider">
                            <a href="#" target="_blank">Keep Digging Deeper</a>
                        </div>
                    </div>
                    <div class="mt-3 max-w-2xl text-sm text-gray-700">
                        QuickerNotes has <a href="#" target="_blank" class="font-bold underline hover:text-gray-900">thorough
                            documentation</a> to show you the ropes. Start digging in to learn how to create properties, manage locations, manage subscriptions, manage staff members, and more.
                    </div>
                </div>
            </div>
        </div>
        <!---->
    </div>
@endsection
