@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-[#10AF13] focus:border-[#10AF13] dark:focus:border-indigo-600 focus:ring-[#10AF13] rounded-md shadow-sm']) }}>
