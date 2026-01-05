<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#10AF13] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0e8e0f] active:bg-[#0e8e0f] focus:outline-none focus:ring-2 focus:ring-[#0e8e0f] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
