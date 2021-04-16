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
                    :amount="$category->images_count"
                    :colors="$category->colors"
                    :deletedAt="$category->deleted_at"/>
        </div>

    </div>

    @auth
        <div class="mt-5 flex lg:mt-0 lg:ml-4 space-x-1">

            {{-- Кнопка "Добавить изображения" --}}
            <x-common.button-add-images
                    :href="route('upload_form', [$category->id])"
                    :title="__('Add images')"/>

            {{-- Кнопка "Редактировать" --}}
            <x-common.button-item-edit
                    :href="route('categories.edit', [$category->id])"
                    :title="__('Edit')"/>

            {{-- Если в категории нет изображений --}}
            @if($category->images_count == 0)

                {{-- Кнопка "Удалить" --}}
                <x-gallery.list-item-delete-form :category="$category">
                    <x-gallery.list-item-button :title="__('Delete')">
                        <!-- Heroicon name: solid/trash -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-red-400"
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

                    {{-- Кнопка "Достать из архива" --}}
                    <x-gallery.list-item-delete-form :category="$category">
                        <x-gallery.list-item-button :title="__('Get it from the archive')">
                            <!-- Heroicon name: outline/receipt-refund -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-purple-400"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                            </svg>
                        </x-gallery.list-item-button>
                    </x-gallery.list-item-delete-form>

                @else

                    {{-- Кнопка "Удалить в архив" --}}
                    <x-gallery.list-item-delete-form :category="$category">
                        <x-gallery.list-item-button :title="__('Delete to archive')">
                            <!-- Heroicon name: outline/archive -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-purple-400"
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

                @endif

            @endif
        </div>
    @endauth
</div>
