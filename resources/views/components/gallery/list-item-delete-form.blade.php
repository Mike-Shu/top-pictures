@props(['category'])

<form action="{{ route('categories.destroy', [$category->id]) }}" method="POST">
    @csrf
    @method('DELETE')
    {{ $slot }}
</form>
