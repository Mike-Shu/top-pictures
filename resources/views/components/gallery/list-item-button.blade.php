@props(['title'])

<button type="submit"
        {!! $attributes->merge(['class' => 'inline-flex items-center p-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']) !!}
        title="{{ $title }}">
    {{ $slot }}
</button>
