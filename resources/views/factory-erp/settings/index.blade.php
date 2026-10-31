@extends('layouts.app')

@section('title', 'Company Settings | TBG ERP')

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-gears text-cyan-400"></i>
                <span>Company Settings & Branding</span>
            </h2>
            <p class="text-xs text-slate-400">Configure company details, main logo (bound to header), and PDF & Print watermark logo (জলছাপ)</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs space-y-1 shadow-lg">
        <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Errors occurred:</div>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-700/60 max-w-4xl">
        <form action="{{ route('factory.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- LOGO SECTION (COMPANY LOGO & PRINT/WATERMARK LOGO) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-2xl bg-[#070e24] border border-slate-800">
                
                <!-- 1. Main Company Logo (Header & Invoices) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-image text-cyan-400"></i>
                            <span>Company Logo (মূল লোগো) *</span>
                        </label>
                        <span class="text-[10px] text-cyan-400 font-mono">Bound to Header & Invoice</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-[#0a1228] border-2 border-dashed border-cyan-500/40 flex items-center justify-center overflow-hidden p-1 shadow-inner relative group">
                            @if(!empty($setting->logo) && file_exists(public_path('storage/' . $setting->logo)))
                                <img id="logoPreview" src="{{ asset('storage/' . $setting->logo) }}" alt="Company Logo" class="max-w-full max-h-full object-contain">
                            @else
                                <img id="logoPreview" src="" alt="Company Logo" class="max-w-full max-h-full object-contain hidden">
                                <div id="logoFallback" class="text-center text-slate-500">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-cyan-400"></i>
                                    <span class="block text-[9px] mt-1 text-slate-400">No Logo</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 space-y-1.5">
                            <input type="file" name="logo" id="logoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   onchange="previewImage(this, 'logoPreview', 'logoFallback')"
                                   class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-600/30 file:text-cyan-300 hover:file:bg-cyan-600/50 file:cursor-pointer cursor-pointer">
                            <p class="text-[10px] text-slate-400">PNG, JPG, WebP, SVG (Max: 4MB). এটি সাইটের উপরে হেডারে দেখাবে।</p>
                        </div>
                    </div>
                </div>

                <!-- 2. PDF & Print Watermark Logo (পিডিএফ ও প্রিন্ট জলছাপ লোগো) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-stamp text-amber-400"></i>
                            <span>PDF & Print Watermark Logo (জলছাপ)</span>
                        </label>
                        <span class="text-[10px] text-amber-400 font-mono">Bound to All Print & PDFs</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-[#0a1228] border-2 border-dashed border-amber-500/40 flex items-center justify-center overflow-hidden p-1 shadow-inner relative group">
                            @php
                                $printLogoSrc = !empty($setting->print_logo) && file_exists(public_path('storage/' . $setting->print_logo))
                                    ? asset('storage/' . $setting->print_logo)
                                    : (!empty($setting->logo) && file_exists(public_path('storage/' . $setting->logo)) ? asset('storage/' . $setting->logo) : null);
                            @endphp

                            @if($printLogoSrc)
                                <img id="printLogoPreview" src="{{ $printLogoSrc }}" alt="Print Watermark" class="max-w-full max-h-full object-contain opacity-60">
                            @else
                                <img id="printLogoPreview" src="" alt="Print Watermark" class="max-w-full max-h-full object-contain opacity-60 hidden">
                                <div id="printLogoFallback" class="text-center text-slate-500">
                                    <i class="fa-solid fa-water text-2xl text-amber-400"></i>
                                    <span class="block text-[9px] mt-1 text-slate-400">No Watermark</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 space-y-1.5">
                            <input type="file" name="print_logo" id="printLogoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                   onchange="previewImage(this, 'printLogoPreview', 'printLogoFallback')"
                                   class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-600/30 file:text-amber-300 hover:file:bg-amber-600/50 file:cursor-pointer cursor-pointer">
                            <p class="text-[10px] text-slate-400">প্রতিটি প্রিন্ট ও পিডিএফ পেজের ঠিক মাঝখানে হালকা জলছাপ (Watermark) হিসেবে প্রদর্শিত হবে।</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- COMPANY INFORMATION INPUTS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Company Name *</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $setting->company_name) }}" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number(s) *</label>
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $setting->email) }}"
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Website URL</label>
                    <input type="text" name="website" value="{{ old('website', $setting->website) }}"
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Factory / Office Address *</label>
                <textarea name="address" rows="3" required
                          class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">{{ old('address', $setting->address) }}</textarea>
            </div>

            <div class="pt-2 flex items-center justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-600/25 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Company Settings & Logos</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function previewImage(input, previewId, fallbackId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                const fallback = document.getElementById(fallbackId);
                if (fallback) fallback.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
