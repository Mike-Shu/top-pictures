{{-- Список категорий для "Сменить категорию". --}}
@if($categories)
    <ul class="w-full bg-white max-h-full rounded-md text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
        @foreach($categories as $_category)
            <li class="change-category-item text-gray-900 cursor-default select-none relative py-2 pr-9 hover:text-white hover:bg-indigo-600">
                <input type="hidden"
                       name="_category_id"
                       value="{{ $_category->id }}">
                <div class="flex items-center">
                    <span class="font-normal mx-3 block truncate">{{ $_category->name }}</span>
                    @if($_category->deleted_at)
                        <span class="text-indigo-600 absolute inset-y-0 right-0 flex items-center pr-4"
                              title="{{ __('Category in the archive') }}">
                            {{-- Heroicon name: solid/archive --}}
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-gray-300"
                                 viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                <path fill-rule="evenodd"
                                      d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
