<div class="uploader-item m-0 p-0 h-14 overflow-hidden animate-fade-in-down">
    <div class="flex items-center pr-1 pt-2">
        <div class="flex-1 mr-2">
            <div class="uploader-item-name text-sm font-medium text-gray-900 mb-1">File name</div>
            <x-progress-bar class="uploader-item-progress"/>
        </div>
        <button class="uploader-item-remove px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 ring-1 ring-red-200 hover:bg-red-200 hover:text-red-900 disabled:opacity-25"
                title="{{ __('Exclude file from the list') }}">X</button>
    </div>
</div>
