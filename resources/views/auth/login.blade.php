<x-guest-layout>
  <x-jet-authentication-card>
    <x-slot name="logo">
      <x-jet-authentication-card-logo />
    </x-slot>

    <x-jet-validation-errors class="mb-4" />

    @if (session('status'))
      <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
      </div>
    @endif

    <h4>Hello! let's get started.</h4>
    <h6 class="font-weight-light">Sign in to continue.</h6>
    <form method="POST" action="{{ route('login') }}" class="pt-3">
      @csrf

      <div class="form-group">
        <input id="email" type="email" name="email" value="{{ old('email') }}"
          class="form-control form-control-lg @error('email') is-invalid @enderror" required
          autocomplete="email" autofocus placeholder="{{ __('Email') }}">
        @error('email')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>

      <div class="form-group">
        <input id="password" type="password" name="password"
          class="form-control form-control-lg @error('password') is-invalid @enderror" required
          autocomplete="current-password" placeholder="{{ __('Password') }}">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
          {{ __('SIGN IN') }}
        </button>
      </div>

      <div class="my-2 row">
        <div class="col-12 col-lg-6">
          <div class="form-check">
            <label class="form-check-label text-muted">
              <input type="checkbox" class="form-check-input" name="remember"
                id="remember" {{ old('remember') ? 'checked' : '' }}>
                {{ __('Keep me signed in') }} <i class="input-helper"></i>
              </label>
          </div>
        </div>

        @if (Route::has('password.request'))
          <div class="col-12 col-lg-6 text-right">
            <a class="auth-link btn p-3 text-black" href="{{ route('password.request') }}"
              onclick="if ($('#email').val()) {$( this ).attr('href', $( this ).attr('href') + '?email=' + $('#email').val());}">
              {{ __('Forgot password?') }}
            </a>
          </div>
        @endif
      </div>

      {{-- <div class="mb-2">
        <button type="button" class="btn btn-block btn-facebook auth-form-btn">
          <i class="mdi mdi-facebook mr-2"></i>Connect using facebook </button>
      </div> --}}

    {{--   <div class="text-center mt-4 font-weight-light">
        Don't have an account?
        @if (Route::has('register'))
          <a class="text-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
        @endif
      </div> --}}

    </form>
  </x-jet-authentication-card>
</x-guest-layout>
