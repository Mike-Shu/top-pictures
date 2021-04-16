@props(['id' => 0, 'selectedId' => 0])

@if($id == $selectedId)
    <option value="{{ $id }}" selected="selected">{{ $slot }}</option>
@else
    <option value="{{ $id }}">{{ $slot }}</option>
@endif
