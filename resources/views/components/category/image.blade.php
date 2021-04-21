<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6 first:-mt-0">
    <div class="p-6 bg-white border-b border-gray-200">
        <img src="{{ $image->thumbs->large->url }}"
             {{ $image->thumbs->large->html }}
             alt="{{ $image->name }}">

        @if($image->palette)
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
                {{-- Основной цвет --}}
                <x-category.meta-info-color-bar class="mr-1" :color="$image->palette->mainColor->color"/>

                @auth
                    {{-- Дополнительные цвета --}}
                    @foreach($image->palette->additionalColors as $_item)
                        <x-category.meta-info-color-bar :color="$_item->color"/>
                    @endforeach
                @endauth
            </x-category.meta-info-item>
        @endif

    </div>
</div>
