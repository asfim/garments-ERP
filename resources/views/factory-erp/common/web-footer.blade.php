@php
    $cmp = $company ?? ($globalCompany ?? \App\Models\CompanySetting::first());
@endphp

<div class="border-t-2 border-[#00adef] pt-3 mt-6 text-[11px] text-slate-800">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
        <!-- Left: Contact Details -->
        <div class="space-y-0.5 text-left">
            <div class="font-bold text-slate-900">
                <span class="text-[#00adef]">Email:</span> {{ $cmp->email ?? 'turkiasugarbd@gmail.com' }}
            </div>
            <div class="text-slate-700">
                <span class="text-[#00adef]">Phone:</span> {{ $cmp->phone ?? '+8801891153903, +8801718521488' }}
            </div>
            <div class="text-sky-700 font-bold">
                <span class="text-[#00adef]">Web:</span> {{ $cmp->website ?? 'Turkiabazar.com' }}
            </div>
        </div>

        <!-- Right: Address & Hotline -->
        <div class="space-y-0.5 sm:text-right">
            <div class="font-bold text-slate-900">
                Head Office: {{ $cmp->address ?? 'Zawad House, House# 73 (1st Floor), Road# 14, Sector# 13, Uttara, Dhaka-1230, Bangladesh.' }}
            </div>
            <div class="text-emerald-700 font-bold">
                Hotline: {{ $cmp->hotline ?? '+8801891-153903' }}
            </div>
        </div>
    </div>
</div>

