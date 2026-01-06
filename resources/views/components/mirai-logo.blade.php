@props(['size' => 'md', 'showText' => true])

@php
$sizes = [
    'sm' => ['svg' => 'w-8 h-8', 'text' => 'text-lg'],
    'md' => ['svg' => 'w-10 h-10', 'text' => 'text-xl'],
    'lg' => ['svg' => 'w-12 h-12', 'text' => 'text-2xl'],
    'xl' => ['svg' => 'w-16 h-16', 'text' => 'text-3xl'],
];
$s = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex items-center gap-3">
    {{-- MIRAI Logo SVG --}}
    <svg viewBox="0 0 130 85" xmlns="http://www.w3.org/2000/svg" class="{{ $s['svg'] }} drop-shadow-[0_0_10px_rgba(6,182,212,0.3)]">
        <defs>
            <linearGradient id="miraiGrad" x1="0" y1="0" x2="130" y2="0" gradientUnits="userSpaceOnUse">
                <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
            </linearGradient>
        </defs>
        <path d="M0,80 L20,0 L45,0 L25,80 Z" fill="url(#miraiGrad)" />
        <path d="M85,80 L105,0 L130,0 L110,80 Z" fill="url(#miraiGrad)" />
        <path d="M54,85 L34,25 L54,25 L64,50 L84,25 L104,25 Z" fill="white" />
    </svg>
    
    @if($showText)
        <span class="font-display font-bold {{ $s['text'] }} bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent tracking-tight">
            MIRAI Hub
        </span>
    @endif
</div>
