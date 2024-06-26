<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body class="bg-blue-800 pt-20">
    <div class="container mx-auto">
        <div class="mb-8 flex flex-col items-center text-white">
            <img class="h-16" src="{{global_asset('/img/quickernotes_logo_white.svg')}}" alt="">
            <p>
                Easy sign up. Be up and running in a few minutes.
            </p>
        </div>
        <form class="flex flex-col items-center" method="POST" action="{{ route('register') }}" id="register-form" name="register-form">
            @csrf
            <div class="w-full max-w-2xl bg-white border rounded p-4 shadow-md">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="business_name">
                            {{ 'Business Name' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="business_name" name="business_name" type="text" placeholder="Acme Pvt Ltd">
                        @if ($errors->has('business_name'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('business_name') }}
                            </p>
                        @endif
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="business_email">
                            {{ 'Business Email' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="business_email" name="business_email" type="text" placeholder="John@acme.com">
                        @if ($errors->has('business_email'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('business_email') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="subdomain">
                            {{ 'Subdomain' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="subdomain" name="subdomain" type="text" placeholder="acme">
                        @if ($errors->has('subdomain'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('subdomain') }}
                            </p>
                        @endif
                    </div>
                </div>


                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="business_phone">
                            {{ 'Business Phone' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="business_phone" name="business_phone" type="text" placeholder="+1 999-999-9999">
                        @if ($errors->has('business_phone'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('business_phone') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>


            <div class="w-full max-w-2xl bg-white border rounded p-4 mt-4 shadow-md">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="first_name">
                            {{ 'First Name' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="first_name" name="first_name" type="text" placeholder="John">
                        @if ($errors->has('first_name'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('first_name') }}
                            </p>
                        @endif
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="last_name">
                            {{ 'Last Name' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="last_name" name="last_name" type="text" placeholder="Doe">
                        @if ($errors->has('last_name'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('last_name') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="admin_email">
                            {{ 'Admin Email' }}
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="admin_email" name="admin_email" type="text" placeholder="John.Doe@acme.com">
                        @if ($errors->has('admin_email'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('admin_email') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="password">
                            Password
                        </label>
                        <input
                            class="w-full appearance-none block bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="password" name="password" type="password" placeholder="******************">
                        @if ($errors->has('password'))
                            <p class="text-red-500 text-xs italic">
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-full max-w-2xl bg-white border rounded p-4 mt-4 shadow-md">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label for="payment"
                               class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Credit
                            Card</label>
                        <div class='max-w-md mb-3' id="card-element">
                        </div>

                        <div id="card-errors" role="alert"i class="text-red-500 text-xs italic"></div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-2xl  mt-4 mb-8">
                <div class="flex flex-wrap">
                    <button type="submit"
                            class=" w-full inline-block align-middle text-center select-none font-bold whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-white bg-gray-800 hover:bg-gray-700">
                        {{ __('Register') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
      var stripe = Stripe('{{config('services.stripe.public_key')}}');
      var elements = stripe.elements();
      var style = {
        base: {
          color: '#32325d',
          fontSize: '16px',
          '::placeholder': {
            color: '#aab7c4'
          }
        },
        invalid: {
          color: '#fa755a',
          iconColor: '#fa755a'
        }
      };

      var card = elements.create('card', {style: style});
      card.mount('#card-element');

      card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
          displayError.textContent = event.error.message;
        } else {
          displayError.textContent = '';
        }
      });

      var form = document.getElementById('register-form');
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
          if (result.error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
          } else {
            stripeTokenHandler(result.token);
          }
        });
      });

      function stripeTokenHandler(token) {
        var form = document.getElementById('register-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_token');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
      }

    </script>
</body>
