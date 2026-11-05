@php
    $qrBase64 = null;
    $qrPath = public_path('images/company_qr.png');
    if (file_exists($qrPath)) {
        $qrBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrPath));
    }
@endphp
<!-- Turkey Bangla Consumer and Food - Official PDF Footer -->
<div class="pdf-footer-wrapper" style="position: fixed; bottom: -65px; left: 0; right: 0; height: 60px; border-top: 3px solid #00adef; padding-top: 6px; font-size: 9px; color: #1e293b; font-family: Helvetica, Arial, sans-serif;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Left: Email, Phone, Website -->
            <td style="width: 42%; vertical-align: middle; text-align: left; line-height: 1.45;">
                <div style="color: #0f172a; font-weight: bold;">
                    <span style="color: #00adef;">Email:</span> {{ $company->email ?? 'turkiasugarbd@gmail.com' }}
                </div>
                <div style="color: #334155;">
                    <span style="color: #00adef;">Phone:</span> {{ $company->phone ?? '+8801891153903, +8801718521488' }}
                </div>
                <div style="color: #0284c7; font-weight: bold;">
                    <span style="color: #00adef;">Web:</span> {{ $company->website ?? 'Turkiabazar.com' }}
                </div>
            </td>

            <!-- Middle: Head Office Address & Hotline -->
            <td style="width: 48%; vertical-align: middle; text-align: right; line-height: 1.45; padding-right: 8px;">
                <div style="color: #0f172a; font-weight: bold;">
                    Head Office: {{ $company->address ?? 'Zawad House, House# 73 (1st Floor), Road# 14, Sector# 13, Uttara, Dhaka-1230, Bangladesh.' }}
                </div>
                <div style="color: #047857; font-weight: bold; margin-top: 2px;">
                    Hotline: {{ $company->hotline ?? '+8801891-153903' }}
                </div>
            </td>

            <!-- Right: Official QR Code -->
            <td style="width: 10%; vertical-align: middle; text-align: right;">
                @if(!empty($qrBase64))
                    <img src="{{ $qrBase64 }}" style="width: 42px; height: 42px; vertical-align: middle;" alt="QR Code">
                @endif
            </td>
        </tr>
    </table>
</div>

