@props(['value'])

<label {{ $attributes->merge(['class' => 'col-md-4 col-form-label text-md-right']) }}>
    {{ $value ?? $slot }}
</label>
