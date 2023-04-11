<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-block btn-lg font-weight-medium']) }}>
    {{ $slot }}
</button>
