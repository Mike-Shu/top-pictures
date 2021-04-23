{{-- Кнопки для управления изображением --}}
<div class="absolute flex m-1 space-x-1">
    <x-gallery.list-item-button :title="__('Change category')">
        <!-- Heroicon name: outline/switch-horizontal -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-5 w-5 text-yellow-700"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
    </x-gallery.list-item-button>
</div>
