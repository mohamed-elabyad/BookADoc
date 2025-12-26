<button {{$attributes->merge(['class' => "px-6 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200"])}}>
    {{$slot}}
</button>
