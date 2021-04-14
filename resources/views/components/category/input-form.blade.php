{{-- Форма для добавления/редактирования категории. --}}
<form method="POST" action="{{ $action }}">
    @csrf
    {{ $slot }}
    <div class="mb-4">
        <x-label-float class="text-xs">0 / {{$nameMaxLength}}</x-label-float>
        <x-label for="category_name" :value="__('Category name')"/>
        <x-input id="category_name"
                 class="block mt-1 w-full"
                 type="text"
                 name="name"
                 maxlength="{{$nameMaxLength}}"
                 placeholder="{{ __('Motorsport') }}"
                 :value="$name"
                 required autofocus/>
        @error('name')
        <x-validation-error :message="$message"/>
        @enderror
    </div>

    <div class="mb-4">
        <x-label-float class="text-xs">0 / {{$descMaxLength}}</x-label-float>
        <x-label for="category_desc" :value="__('Category description')"/>
        <x-textarea id="category_desc"
                    name="description"
                    rows="3"
                    maxlength="{{ $descMaxLength }}"
                    class="block mt-1 w-full"
                    :text="$description"
                    placeholder="{{ __('Racing motorcycles, cars and trucks.') }}"/>
        @error('description')
        <x-validation-error :message="$message"/>
        @enderror
    </div>

    <div class="mt-4">

        @if($updateMode)
            <x-button type="submit" class="w-52 justify-center">{{ __('Save') }}</x-button>
        @else
            <x-button type="submit" class="w-52 justify-center">{{ __('Add') }}</x-button>
        @endif

        @if (session('message'))
            <span>{{ session('message') }}</span>
        @endif

    </div>

</form>