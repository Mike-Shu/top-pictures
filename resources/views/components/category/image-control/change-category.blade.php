{{-- Блок: "Изменить категориюЭ --}}
@props(['image'])

<div class="change-category relative"
     x-data="{ open: false }"
     @click.away="open = false"
     @close.stop="open = false"
     @keydown.escape="open = false">

    <input type="hidden"
           name="_image_id"
           value="{{ $image->id }}">

    {{-- Кнопка (при клике проверяем её на "disabled") --}}
    <div @click="if (!$el.closest('.change-category').querySelector('.change-category-button').disabled) open = !open">
        <x-gallery.list-item-button :title="__('Change category')" class="change-category-button">
            {{-- Heroicon name: outline/switch-horizontal --}}
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 text-yellow-700 transform rotate-45"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </x-gallery.list-item-button>
    </div>

    {{-- Список категорий --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 min-w-48 rounded-md shadow-lg origin-top-left left-0"
         style="display: none;"
         @click="open = false">
        <div class="change-category-content rounded-md ring-1 ring-black ring-opacity-5 p-0 bg-white">
            {{-- JS-логика в "image/CategoryChanger.js" --}}
        </div>
    </div>

</div>