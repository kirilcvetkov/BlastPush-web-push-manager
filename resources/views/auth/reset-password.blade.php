<x-guest-layout>
  <x-jet-authentication-card>
    <x-slot name="logo">
      <x-jet-authentication-card-logo />
    </x-slot>

    <h4>{{ __('Reset Password') }}</h4>
    <h6 class="font-weight-light">Type in the new password for your account.</h6>

    <x-jet-validation-errors class="mt-4" />

    <form method="POST" action="{{ route('password.update') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <div class="form-group mt-4">
        <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" placeholder="{{ __('Email') }}" required autofocus />
        </div>

        <div class="form-group mt-4">
          <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}" />
        </div>

        <div class="form-group mt-4">
          <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}"/>
        </div>

        <div class="mt-4">
          <x-jet-button class="btn-gradient-danger">
            {{ __('Reset Password') }}
          </x-jet-button>
        </div>
      </form>
    </x-jet-authentication-card>
  </x-guest-layout>
