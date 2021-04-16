<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6 first:-mt-0">
    <div class="p-6 bg-white border-b border-gray-200">
        <img src="{{ $image->thumbs->large->url }}"
             {{ $image->thumbs->large->html }}
             alt="{{ $image->name }}">
    </div>
</div>
