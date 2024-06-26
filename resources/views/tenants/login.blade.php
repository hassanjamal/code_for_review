@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="w-full max-w-sm">
                <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">
                    
                    <div class="font-semibold bg-gray-200 text-gray-700 py-3 px-6 mb-0">
                        {{ __('Admin Login') }}
                    </div>
                    
                    <form class="w-full p-6" method="POST" action="">
                        @csrf
                        <div class="flex flex-wrap mb-6">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('E-Mail Address') }}:R
                            </label>
                            
                            <input id="email" type="email"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('email') ? ' border-red-500' : '' }}"
                                   name="email" value="{{ old('email') }}" required autofocus>
                            
                            @if ($errors->has('email'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('email') }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap mb-6">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('Password') }}:
                            </label>
                            
                            <input id="password" type="password"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('password') ? ' border-red-500' : '' }}"
                                   name="password" required>
                            
                            @if ($errors->has('password'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex mb-6">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            
                            <label class="text-sm text-gray-700 ml-3" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                        
                        <div class="flex flex-wrap items-center">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Login') }}
                            </button>
                        
                        </div>
                    </form>
                
                </div>
            </div>
        </div>
        <div class="flex flex-wrap justify-center mt-10">
            <div class="w-full max-w-sm">
                <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">
                    
                    <div class="font-semibold bg-gray-200 text-gray-700 py-3 px-6 mb-0">
                        {{ __('Login With MINDBODY') }}
                    </div>
                    
                    <form class="w-full p-6" method="POST" action="{{route('login.mindbody')}}">
                        @csrf
                        <div class="flex flex-wrap mb-6">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('MINDBODY Username') }}:
                            </label>
                            
                            <input id="username" type="text"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('username') ? ' border-red-500' : '' }}"
                                   name="username" value="{{ old('username') }}" required autofocus>
                            
                            @if ($errors->has('username'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('username') }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap mb-6">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('MINDBODY Password') }}:
                            </label>
                            
                            <input id="password" type="password"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('password') ? ' border-red-500' : '' }}"
                                   name="password" required>
                            
                            @if ($errors->has('password'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap mb-6">
                            <label for="site_id" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('MINDBODY Site ID') }}:
                            </label>
                            
                            <input id="site_id" type="text"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('site_id') ? ' border-red-500' : '' }}"
                                   name="site_id" required>
                            
                            @if ($errors->has('site_id'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('site_id') }}
                                </p>
                            @endif
                        </div>
                        
                        
                        
                        <div class="flex mb-6">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            
                            <label class="text-sm text-gray-700 ml-3" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                        
                        <div class="flex flex-wrap items-center">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Login') }}
                            </button>
                        
                        </div>
                    </form>
                
                </div>
            </div>
        </div>
    </div>
@endsection
