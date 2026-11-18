@props(['name', 'role' => null, 'avatar' => null, 'content', 'rating' => 5, 'demo' => false])

<figure class="flex h-full flex-col gap-4 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
    <div class="flex items-center gap-1" role="img" aria-label="Rating {{ $rating }} dari 5">
        @for ($i = 1; $i <= 5; $i++)
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 {{ $i <= (int) $rating ? 'text-amber-500' : 'text-stone-300' }}" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/></svg>
        @endfor
        @if ($demo)
            <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">Demo</span>
        @endif
    </div>

    <blockquote class="text-sm leading-relaxed text-stone-600">{{ $content }}</blockquote>

    <figcaption class="mt-auto flex items-center gap-3">
        @if ($avatar)
            <img src="{{ $avatar }}" alt="{{ $name }}" class="h-10 w-10 rounded-full object-cover" loading="lazy">
        @else
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800" aria-hidden="true">{{ strtoupper(substr($name, 0, 1)) }}</span>
        @endif
        <span>
            <span class="block text-sm font-semibold text-stone-900">{{ $name }}</span>
            @if ($role)
                <span class="block text-xs text-stone-500">{{ $role }}</span>
            @endif
        </span>
    </figcaption>
</figure>
