{{-- "Основной цвет" для отображения в категориях. --}}
<div {!! $attributes->merge(['class' => 'border-0 rounded h-4 w-4 sm:h-5 sm:w-5']) !!}
     style="background-color: {{ $color }}"></div>
