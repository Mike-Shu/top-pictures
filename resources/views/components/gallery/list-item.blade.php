{{-- Объект "категория" для списка на странице "Галерея". --}}
<div class="lg:flex lg:items-center lg:justify-between my-4 first:-mt-0 pt-4">

    <div class="flex-1 min-w-0">

        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            <a href="{{ route('categories.show', [$category->id]) }}">
                {{ $category->name }}
            </a>
        </h2>

        @if($category->description)
            {{ $category->description }}
        @endif

        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
            <x-category.meta-info
                    :amount="$category->amount"
                    :colors="$category->colors"
                    :deletedAt="$category->deleted_at"/>
        </div>

    </div>

    @auth
        <div class="mt-5 flex lg:mt-0 lg:ml-4">

            {{-- Кнопка "Добавить изображения" --}}
            <a href="{{ route('upload_form', [$category->id]) }}">
                <x-gallery.list-item-button :title="__('Add images')">
                    <!-- Heroicon name: solid/view-grid-add -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5 text-gray-500"
                         viewBox="0 0 20 20"
                         fill="currentColor">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                    </svg>
                </x-gallery.list-item-button>
            </a>

            &nbsp;
            {{-- Кнопка "Редактировать" --}}
            <a href="{{ route('categories.edit', [$category->id]) }}">
                <x-gallery.list-item-button :title="__('Edit')">
                    <!-- Heroicon name: solid/pencil -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5 text-gray-500"
                         viewBox="0 0 20 20"
                         fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </x-gallery.list-item-button>
            </a>

            {{-- Если в категории нет изображений --}}
            @if($category->amount == 0)

                &nbsp;
                {{-- Кнопка "Удалить" --}}
                <x-gallery.list-item-delete-form :category="$category">
                    <x-gallery.list-item-button :title="__('Delete')">
                        <!-- Heroicon name: solid/trash -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-500"
                             viewBox="0 0 20 20"
                             fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </x-gallery.list-item-button>
                </x-gallery.list-item-delete-form>

            @else

                @if($category->deleted_at)

                    &nbsp;
                    {{-- Кнопка "Достать из архива" --}}
                    <x-gallery.list-item-delete-form :category="$category">
                        <x-gallery.list-item-button :title="__('Get it from the archive')">
                            <!-- Heroicon name: outline/archive -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-green-400"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </x-gallery.list-item-button>
                    </x-gallery.list-item-delete-form>

                @else

                    &nbsp;
                    {{-- Кнопка "Удалить в архив" --}}
                    <x-gallery.list-item-delete-form :category="$category">
                        <x-gallery.list-item-button :title="__('Delete to archive')">
                            <!-- Heroicon name: solid/archive -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-red-300"
                                 viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                <path fill-rule="evenodd"
                                      d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </x-gallery.list-item-button>
                    </x-gallery.list-item-delete-form>

                @endif

            @endif
        </div>
    @endauth
</div>
