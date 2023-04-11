<x-guest-layout>
  <x-jet-authentication-card>
    <x-slot name="logo">
      <x-jet-authentication-card-logo />
    </x-slot>

    <h4>{{ __('Forgot your password?') }}</h4>
    <h6 class="font-weight-light">
      {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </h6>

    @if (session('status'))
      <div class="alert alert-success mt-4" role="alert">
        <p class="font-weight-light m-0">{{ session('status') }}</p>
      </div>
    @else

    <x-jet-validation-errors class="mt-4" />

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="form-group mt-4">
        <x-jet-input id="email" class="form-control-lg"
          type="email" name="email" :value="old('email')" placeholder="{{ __('Email') }}" required autofocus />
      </div>

      <div class="mt-4">
        <x-jet-button class="btn-gradient-danger">
          {{ __('Email Password Reset Link') }}
        </x-jet-button>
      </div>
    </form>

    @endif
  </x-jet-authentication-card>
</x-guest-layout>
