@props(['name', 'options', 'required' => null])

<select name="{{ $name }}" id="{{ $name }}" @if ($required) required @endif
    class="block w-full rounded-md border-0 py-2 px-3 text-sm text-gray-700 placeholder-gray-400
    ring-1 ring-gray-300 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0
    shadow-sm hover:ring-gray-400 transition duration-150 ease-in-out">
    <option value="" disabled hidden @selected(!request($name))>{{ ucFirst($name) }}</option>
    @foreach ($options as $option)
        <option value="{{ $option }}" @selected($option === request($name))>{{ ucFirst($option) }}</option>
    @endforeach
</select>
