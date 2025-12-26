@props(['name' => null, 'placeholder'=> null, 'value'=> null, 'type' => 'text', 'required' => null])

<div class="relative" x-data="">

    <button type="button" class="absolute top-0 right-0 flex h-full items-center pr-2"
        @click="$refs['input-{{$name}}'].value='';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="h-4 w-4 text-slate-500">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <input x-ref="input-{{ $name }}" type="{{$type}}" placeholder="{{ $placeholder }}"
        name="{{ $name }}" value="{{$value}}" id="{{ $name }}"   @if($required) required @endif
        @class(['w-full rounded-md border-0 py-1.5 px-2.5 pr-8 text-sm  placeholder:text-slate-400 block
                w-full rounded-md border-0 py-2 px-3 text-sm text-gray-700 placeholder-gray-400
                ring-1 ring-gray-300 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0
                shadow-sm hover:ring-gray-400 transition duration-150 ease-in-out ',
        'ring-slate-300' => !$errors->has($name),
        'ring-red-300' => $errors->has($name)])
/>

</div>
<div>
    @error($name)
        <div class="mt-1 text-xs text-red-500">
            {{$message}}
        </div>
    @enderror
</div>
