<x-app-layout>
    <x-slot name="header">

        @auth
            <div class="float-right">
                {{-- Кнопка "Редактировать" --}}
                <a href="{{ route('categories.edit', [$category->id]) }}">
                    <x-gallery.list-item-button class="p-1" :title="__('Edit')">
                        <!-- Heroicon name: solid/pencil -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-500"
                             viewBox="0 0 20 20"
                             fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                    </x-gallery.list-item-button>
                </a>
            </div>
        @endauth

        <h1 class="mb-1 font-semibold text-2xl text-gray-800 leading-tight sm:text-3xl sm:mb-2">
            {{ $category->name }}
        </h1>

        @if($category->description)
            {{ $category->description }}
        @endif

        <div class="mt-0 flex flex-col sm:flex-row sm:flex-wrap sm:mt-1 sm:space-x-6">
            <x-category.meta-info
                    :amount="$category->amount"
                    :colors="$category->colors"
                    :deletedAt="$category->deleted_at"/>
        </div>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
