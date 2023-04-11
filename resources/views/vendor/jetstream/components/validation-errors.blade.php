@if ($errors->any())
    <div {{ $attributes->merge(['type' => 'submit', 'class' => 'alert alert-danger']) }} role="alert">
        @foreach ($errors->all() as $error)
            <p class="font-weight-light m-0">{{ $error }}</p>
        @endforeach
    </div>
@endif
