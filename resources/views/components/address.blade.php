@props(['address'])
<div class="flex justify-center items-baseline gap-2 mb-5">
    <svg xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5 text-red-500 flex-shrink-0"
        fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5
        c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5
        2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
    </svg>

    <span {{$attributes->merge(['class', "text-slate-700 leading-tight"])}}>
            {{$address}}
    </span>
</div>
