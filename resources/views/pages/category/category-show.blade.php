<x-app-layout>
    <x-slot name="header">

        @auth
            <div class="float-right space-x-1">
                {{-- Кнопка "Добавить изображения" --}}
                <x-common.button-add-images
                        :href="route('upload_form', [$category->id])"
                        :title="__('Add images')"/>

                {{-- Кнопка "Редактировать" --}}
                <x-common.button-item-edit
                        :href="route('categories.edit', [$category->id])"
                        :title="__('Edit')"/>
            </div>
        @endauth

        <h1 class="mb-1 font-semibold text-2xl text-gray-800 leading-tight sm:text-3xl sm:mb-2">
            {{ $category->name }}
        </h1>

        @if($category->description)
            {{ $category->description }}
        @endif

        <div class="mt-0 flex flex-col items-start sm:flex-row sm:flex-wrap sm:mt-1 sm:space-x-6">
            <x-category.meta-info.meta-info
                    :amount="$category->images_count"
                    :colors="$category->colors"
                    :deletedAt="$category->deleted_at"/>
        </div>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($images)

                @if($paginator->hasPages())
                    <div class="pb-6">
                        {{ $paginator->links() }}
                    </div>
                @endif

                <div class="space-y-6">
                    @foreach($images as $_image)
                        <x-category.image :image="$_image"/>
                    @endforeach
                </div>

                @if($paginator->hasPages())
                    <div class="pt-6">
                        {{ $paginator->links() }}
                    </div>
                @endif

            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        {{ __('Not Found') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @auth
        {{-- Управление изображением: изменить категорию --}}
        <input type="hidden"
               name="_categories_list_url"
               value="{{ route('image-categories-list', [$category->id]) }}">

        <input type="hidden"
               name="_change_category_url"
               value="{{ route('image-change-category') }}">

        <input type="hidden"
               name="_change_main_color_url"
               value="{{ route('image-change-main-color') }}">

        @push('scripts')
            <script src="{{ asset('js/change-category.js') }}"></script>
            <script src="{{ asset('js/change-main-color.js') }}"></script>
        @endpush
        {{-- /Изменить категорию --}}
    @endauth

</x-app-layout>
