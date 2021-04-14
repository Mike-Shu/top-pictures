{{-- Блок с метаданными о категории. --}}
<x-category.meta-info-item>
    <!-- Heroicon name: solid/photograph -->
    <svg xmlns="http://www.w3.org/2000/svg"
         class="flex-shrink-0 mr-1 h-4 w-4 sm:mr-1.5 sm:h-5 sm:w-5"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <span class="text-xs sm:text-base">{{ $amount }}</span>
</x-category.meta-info-item>

@if($colors)
    <x-category.meta-info-item>
        <!-- Heroicon name: solid/color-swatch -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="flex-shrink-0 mr-1 h-4 w-4 sm:mr-1.5 sm:h-5 sm:w-5"
             viewBox="0 0 20 20"
             fill="currentColor">
            <path fill-rule="evenodd"
                  d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z"
                  clip-rule="evenodd"/>
        </svg>
        @if($colors)
            @foreach($colors as $_item)
                <x-category.meat-info-color-bar :color="$_item->color"/>
            @endforeach
        @endif
    </x-category.meta-info-item>
@endif

@if($deletedAt)
    <x-category.meta-info-item>
        <!-- Heroicon name: solid/archive -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="flex-shrink-0 mr-1 h-4 w-4 sm:mr-1.5 sm:h-5 sm:w-5"
             viewBox="0 0 20 20"
             fill="currentColor">
            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
            <path fill-rule="evenodd"
                  d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                  clip-rule="evenodd"/>
        </svg>
        <span class="text-xs sm:text-base">{{ __('Category in the archive') }}</span>
    </x-category.meta-info-item>
@endif