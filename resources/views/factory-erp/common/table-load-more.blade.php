@php
    $pageSize = $pageSize ?? 10;
    $itemName = $itemName ?? 'Records';
    $color = $color ?? 'cyan';
    $colorGradients = [
        'cyan' => 'from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 shadow-cyan-950/50',
        'indigo' => 'from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 shadow-indigo-950/50',
        'purple' => 'from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 shadow-purple-950/50',
        'emerald' => 'from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-950/50',
        'rose' => 'from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 shadow-rose-950/50',
        'amber' => 'from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 shadow-amber-950/50',
    ];
    $gradientClass = $colorGradients[$color] ?? $colorGradients['cyan'];
@endphp

<div id="{{ $id }}LoadMoreContainer" class="mt-4 pt-3 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3">
    <div class="text-xs text-slate-400 font-mono">
        Showing <span id="{{ $id }}VisibleCount" class="text-{{ $color }}-400 font-bold">{{ min($pageSize, $total) }}</span> of <span id="{{ $id }}TotalCount" class="text-white font-bold">{{ $total }}</span> {{ $itemName }}
    </div>

    <div class="flex items-center gap-2">
        <button type="button" 
                id="{{ $id }}LoadMoreBtn" 
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r {{ $gradientClass }} text-white text-xs font-bold shadow-lg flex items-center gap-2 transition-all cursor-pointer {{ $total <= $pageSize ? 'hidden' : '' }}">
            <i class="fa-solid fa-circle-down"></i>
            <span>Load More {{ $itemName }} (+{{ $pageSize }})</span>
        </button>
        <div id="{{ $id }}AllLoadedMsg" class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5 {{ $total <= $pageSize ? '' : 'hidden' }}">
            <i class="fa-solid fa-check text-emerald-400"></i> All {{ strtolower($itemName) }} loaded
        </div>
    </div>
</div>

