{{--
    A screenshot inside a fake browser window.
    Usage: <x-browser-frame src="images/landing/dashboard.png" alt="..." :width="1280" :height="1078" />
    Add :crop="true" to show a fixed-shape slice from the top, and :eager="true" for images above the fold.
--}}
@props(['src', 'alt', 'width', 'height', 'crop' => false, 'eager' => false])

<div {{ $attributes->class(['overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl shadow-indigo-900/10']) }}>
    <div class="flex items-center gap-1.5 border-b border-gray-100 bg-gray-50 px-4 py-2.5" aria-hidden="true">
        <span class="h-2.5 w-2.5 rounded-full bg-red-300"></span>
        <span class="h-2.5 w-2.5 rounded-full bg-yellow-300"></span>
        <span class="h-2.5 w-2.5 rounded-full bg-green-300"></span>
    </div>
    <img src="{{ asset($src) }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}"
        loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async"
        class="block w-full {{ $crop ? 'aspect-[16/10] object-cover object-top' : 'h-auto' }}">
</div>
