@props([
    'href' => '#',
    'title' => '',
    ])

<a href="{{ $href }}">
    <x-gallery.list-item-button class="p-1" :title="$title">
        <!-- Heroicon name: solid/pencil -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-5 w-5 text-blue-400"
             viewBox="0 0 20 20"
             fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
        </svg>
    </x-gallery.list-item-button>
</a>
