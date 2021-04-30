<div class="category-image bg-white overflow-hidden shadow-sm sm:rounded-lg first:-mt-0">
    <div class="p-6 bg-white border-b border-gray-200">

        @auth
            {{-- Управление изображением --}}
            <x-category.image-control.image-control :image="$image"/>
        @endauth

        <input type="hidden"
               name="_image_id"
               value="{{ $image->id }}">

        <a href="{{ route('download', [$image->name]) }}"
           target="_blank"
           title="{{ __('Click to download') }}">
            <img src="{{ $image->thumbs->large->url }}"
                 {{ $image->thumbs->large->html }}
                 alt="{{ $image->name }}">
        </a>

        <div class="flex mt-2 space-x-4">

            {{-- Основной цвет изображения --}}
            @if($image->palette)
                <x-category.meta-info.item>
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
                    <x-category.meta-info.color-bar
                            class="main-color mr-1"
                            :color="$image->palette->mainColor->color"/>

                    @auth
                        {{-- Дополнительные цвета --}}
                        @foreach($image->palette->additionalColors as $_item)
                            <x-category.meta-info.color-bar
                                    class="additional-color cursor-pointer"
                                    :color="$_item->color"/>
                        @endforeach
                    @endauth
                </x-category.meta-info.item>
            @endif

            {{-- Разрешение картинки --}}
            <x-category.meta-info.item>
                <!-- Heroicon name: solid/arrows-expand -->
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="flex-shrink-0 mr-0 h-4 w-4 sm:mr-0.5 sm:h-5 sm:w-5"
                     viewBox="0 0 20 20"
                     fill="currentColor">
                    <path stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 8V4m0 0h4M3 4l4 4m8 0V4m0 0h-4m4 0l-4 4m-8 4v4m0 0h4m-4 0l4-4m8 4l-4-4m4 4v-4m0 4h-4"/>
                </svg>
                <x-category.meta-info.value>{{ $image->width }}x{{ $image->height }}</x-category.meta-info.value>
            </x-category.meta-info.item>

            {{-- Размер файла --}}
            <x-category.meta-info.item>
                <!-- Heroicon name: solid/save -->
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="flex-shrink-0 mr-1 h-4 w-4 sm:mr-1.5 sm:h-5 sm:w-5"
                     viewBox="0 0 20 20"
                     fill="currentColor">
                    <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"/>
                </svg>
                <x-category.meta-info.value>{{ $image->size }}</x-category.meta-info.value>
            </x-category.meta-info.item>

        </div>

    </div>
</div>
