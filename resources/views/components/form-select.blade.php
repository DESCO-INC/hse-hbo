<select {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 outline-none']) }} required>
    {{ $slot }}
</select>
