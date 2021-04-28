{{-- Кнопки для управления изображением --}}
@props(['image'])

<div class="absolute flex m-1 space-x-1">
    {{-- Изменить категорию --}}
    <x-category.image-control.change-category :image="$image"/>
</div>
