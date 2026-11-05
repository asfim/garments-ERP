@php
    $company = $company ?? ($globalCompany ?? \App\Models\CompanySetting::first());

    // 1. Logo Base64
    $logoBase64 = $logoBase64 ?? null;
    if (!$logoBase64 && !empty($company?->logo)) {
        $logoFile = public_path('storage/' . $company->logo);
        if (file_exists($logoFile)) {
            $ext = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
        }
    }

    // 2. Watermark Logo Base64 (জলছাপ - Bound to /factory/settings)
    $watermarkBase64 = $watermarkBase64 ?? null;
    if (!$watermarkBase64) {
        $wmFile = !empty($company?->print_logo) && file_exists(public_path('storage/' . $company->print_logo))
            ? public_path('storage/' . $company->print_logo)
            : (!empty($company?->logo) && file_exists(public_path('storage/' . $company->logo)) ? public_path('storage/' . $company->logo) : null);

        if ($wmFile && file_exists($wmFile)) {
            $ext = strtolower(pathinfo($wmFile, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
            $watermarkBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($wmFile));
        }
    }
@endphp

<!-- Watermark / Jolchap (Repeats on every PDF page in background) -->
@if(!empty($watermarkBase64))
<div style="position: fixed; top: 22%; left: 10%; width: 80%; text-align: center; z-index: -1; pointer-events: none;">
    <img src="{{ $watermarkBase64 }}" style="width: 430px; max-width: 80%; height: auto;" alt="Company Watermark">
</div>
@endif

<!-- Top Left Cyan Geometric Accent Triangle (Exact Image 2 Match) -->
<div style="position: absolute; top: -25px; left: -25px; width: 0; height: 0; border-top: 60px solid #00adef; border-right: 85px solid transparent; z-index: 5;"></div>

<!-- Company Header Branding -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
    <tr>
        <td style="text-align: center; vertical-align: middle;">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" style="max-height: 52px; max-width: 130px; vertical-align: middle; margin-right: 12px;" alt="Logo">
            @endif
            <div style="display: inline-block; vertical-align: middle; text-align: left;">
                <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: 0.5px; font-family: Helvetica, Arial, sans-serif;">
                    {{ $company->company_name ?? 'Turkey Bangla Consumer and Food' }}
                </h1>
                <div style="font-size: 11px; font-style: italic; color: #475569; font-weight: bold; margin-top: 2px;">
                    {{ $company->tagline ?? 'Quality is our Priority' }}
                </div>
            </div>
        </td>
    </tr>
</table>

<!-- Subheader Strip: Trade Licence & TIN No. with Cyan Divider Line -->
<table style="width: 100%; border-collapse: collapse; border-bottom: 2.5px solid #00adef; padding-bottom: 5px; margin-bottom: 14px; font-size: 11px; color: #0f172a; font-weight: bold; font-family: Helvetica, Arial, sans-serif;">
    <tr>
        <td style="text-align: left;">
            Trade Licence No. {{ $company->trade_licence ?? '019268' }}
        </td>
        <td style="text-align: right;">
            TIN No. {{ $company->tin_no ?? '369123229836' }}
        </td>
    </tr>
</table>
