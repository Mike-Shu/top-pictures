<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gallery') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 divide-y">

                    @if($items)
                        @php($show = (count($items) == $paginator->perPage()))

                        @if($paginator->hasPages() && $show)
                            <div class="pb-2">
                                {{ $paginator->links() }}
                            </div>
                        @endif

                        @foreach($items as $_category)
                            <x-gallery.list-item :category="$_category"/>
                        @endforeach

                        @if($paginator->hasPages())
                            <div class="pt-6">
                                {{ $paginator->links() }}
                            </div>
                        @endif
                    @else
                        {{ __('Not Found') }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
