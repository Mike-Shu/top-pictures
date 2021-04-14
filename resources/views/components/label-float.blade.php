@props(['value'])

<span {!! $attributes->merge(['class' => 'float-right text-gray-500']) !!}>
    {{ $value ?? $slot }}
</span>
