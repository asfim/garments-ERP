@php
    $cmp = $company ?? ($globalCompany ?? \App\Models\CompanySetting::first());

    // Logo (prefer Base64 or local asset)
    $logoSrc = null;
    if (!empty($cmp?->logo)) {
        $logoFile = public_path('storage/' . $cmp->logo);
        if (file_exists($logoFile)) {
            $ext = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
        }
    }

    // Watermark Logo Base64 (জলছাপ - Bound to /factory/settings)
    $watermarkSrc = null;
    $wmFile = !empty($cmp?->print_logo) && file_exists(public_path('storage/' . $cmp->print_logo))
        ? public_path('storage/' . $cmp->print_logo)
        : (!empty($cmp?->logo) && file_exists(public_path('storage/' . $cmp->logo)) ? public_path('storage/' . $cmp->logo) : null);

    if ($wmFile && file_exists($wmFile)) {
        $ext = strtolower(pathinfo($wmFile, PATHINFO_EXTENSION));
        $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
        $watermarkSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($wmFile));
    }
@endphp

<!-- Watermark / Jolchap for Browser Print (Bound to /factory/settings) -->
@if($watermarkSrc)
<div class="watermark-container pointer-events-none select-none absolute inset-0 flex items-center justify-center overflow-hidden z-0" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 0; opacity: 0.16;">
    <img src="{{ $watermarkSrc }}" alt="Company Watermark" class="max-w-[70%] max-h-[60%] object-contain" style="mix-blend-mode: multiply; opacity: 0.16;">
</div>
@endif

<!-- Top Left Cyan Geometric Accent Triangle (Exact Image 2 Match) -->
<div style="position: absolute; top: 0; left: 0; width: 0; height: 0; border-top: 55px solid #00adef; border-right: 75px solid transparent; z-index: 5;" aria-hidden="true"></div>

<!-- Brand Header -->
<div class="text-center relative z-10 pb-2 mb-2">
    <div class="inline-flex items-center justify-center gap-3">
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="h-12 w-auto max-w-[130px] object-contain">
        @endif
        <div class="text-left">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-wide uppercase font-sans">
                {{ $cmp->company_name ?? 'Turkey Bangla Consumer and Food' }}
            </h1>
            <p class="text-xs italic text-slate-600 font-semibold mt-0.5">
                {{ $cmp->tagline ?? 'Quality is our Priority' }}
            </p>
        </div>
    </div>
</div>

<!-- Subheader Bar: Trade Licence & TIN No. with Cyan Divider Line -->
<div class="flex items-center justify-between border-b-2 border-[#00adef] pb-2 mb-4 text-xs font-bold text-slate-800">
    <span>Trade Licence No. {{ $cmp->trade_licence ?? '019268' }}</span>
    <span>TIN No. {{ $cmp->tin_no ?? '369123229836' }}</span>
</div>
