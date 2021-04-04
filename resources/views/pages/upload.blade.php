<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload a photo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div id="uploader">

                        @csrf
                        <input name="_config" type="hidden" value="{{ $config }}">
                        <input name="_item_url" type="hidden" value="{{ route('ajax_uploader_list_item') }}">
                        <input name="_upload_url" type="hidden" value="{{ route('ajax_upload_file') }}">
                        <input name="_not_supported_url" type="hidden"
                               value="{{ route('ajax_uploader_not_supported') }}">

                        <div id="drop-area"
                             class="flex items-center justify-center border-4 border-gray-100 border-dashed h-16">
                            <div>
                                {!! __('Drop image files here to upload or :link.', ['link' => '<span class="cursor-pointer text-indigo-700 hover:text-indigo-500" id="browse-button">' . __('select from your computer') . '</span>']) !!}
                            </div>
                        </div>

                        <div class="mt-4 slider-hidden" id="uploading-container">
                            <h2 class="text-xl">
                                {{ __('Files for uploading') }} (<span id="uploading-list-count">0</span>)
                            </h2>
                            <div class="flex space-x-2 mt-6" id="uploader-list-control">
                                <x-button id="uploader-start-list"
                                          class="w-52 justify-center">{{ __('Upload files') }}</x-button>
                                <x-button id="uploader-clear-list"
                                          class="w-52 justify-center">{{ __('Clear the list') }}</x-button>
                                <x-button id="uploader-stop-list"
                                          class="w-52 justify-center">{{ __('Stop uploading') }}</x-button>
                            </div>
                            <div id="uploading-list" class="mt-3"></div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')<script src="{{ asset('js/upload.js') }}"></script>@endpush

</x-app-layout>
