@extends('layouts.app')

@section('title', 'Supplier Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-indigo-400"></i>
                Supplier & Purchase Registry
            </h2>
            <p class="text-xs text-slate-400">Track suppliers, contact details, address, and purchased products (কোন সাপ্লায়ার থেকে কোন প্রোডাক্ট কিনেছি)</p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- CSV Download Button -->
            <a href="{{ route('factory.suppliers.export.csv') }}" 
               title="Export Suppliers to CSV"
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-emerald-400 hover:text-emerald-300 border border-slate-700 hover:border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-sm"></i>
                <span>Download CSV</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Suppliers</span>
                <span class="text-xl font-mono font-extrabold text-white mt-1 block">{{ $totalSuppliersCount }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                <i class="fa-solid fa-truck-ramp-box text-base"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Linked Products</span>
                <span class="text-xl font-mono font-extrabold text-cyan-400 mt-1 block">{{ $totalLinkedProducts }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-boxes-stacked text-base"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Available Products in System</span>
                <span class="text-xl font-mono font-extrabold text-amber-400 mt-1 block">{{ $products->count() }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-tags text-base"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Form on Left, Table on Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Add New Supplier Form -->
        <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 h-fit">
            <div class="flex items-center justify-between mb-3.5 pb-2.5 border-b border-slate-700/70">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-400"></i>
                    <span>Add New Supplier</span>
                </h3>
                <span class="text-[10px] text-slate-400 uppercase font-mono tracking-wider">Purchase Entry</span>
            </div>

            <form action="{{ route('factory.suppliers.store') }}" method="POST" class="space-y-3.5">
                @csrf
                
                <!-- Supplier Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Supplier Name *
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-building absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="name" placeholder="e.g. Meghna Yarn & Fabric Ltd" required
                               value="{{ old('name') }}"
                               class="w-full bg-[#0a1228] text-xs text-white rounded-xl pl-9 pr-3.5 py-2.5 border border-slate-700 focus:border-indigo-400 focus:outline-none">
                    </div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Phone Number *
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="phone" placeholder="e.g. +880 1823-112233" required
                               value="{{ old('phone') }}"
                               class="w-full bg-[#0a1228] text-xs text-white rounded-xl pl-9 pr-3.5 py-2.5 border border-slate-700 focus:border-indigo-400 focus:outline-none">
                    </div>
                </div>

                <!-- Product Name: Custom Searchable Dropdown with Live Search -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Purchased Product Name *
                        </label>
                        <a href="{{ route('factory.products.index') }}" class="text-[10px] text-cyan-400 hover:underline">
                            + New Product
                        </a>
                    </div>
                    
                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="product_id" id="createProductId" value="{{ old('product_id') }}" required>

                    <!-- Trigger Button -->
                    <button type="button" id="createProductDropdownBtn" onclick="toggleDropdown('create')"
                            class="w-full bg-[#0a1228] text-xs text-left rounded-xl px-3.5 py-2.5 border border-slate-700 hover:border-indigo-400 focus:outline-none flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <i class="fa-solid fa-box text-cyan-400 text-xs flex-shrink-0"></i>
                            <span id="createProductLabel" class="text-slate-400 truncate">
                                -- Select / Search Product --
                            </span>
                        </div>
                        <i id="createProductChevron" class="fa-solid fa-chevron-down text-slate-400 text-[11px] transition-transform"></i>
                    </button>

                    <!-- Searchable Dropdown Menu -->
                    <div id="createProductDropdownMenu" 
                         class="hidden absolute z-30 left-0 right-0 top-full mt-1.5 rounded-2xl bg-[#0a1228]/95 border border-slate-700/80 shadow-2xl backdrop-blur-md p-2 space-y-2">
                        
                        <!-- Live Search Input -->
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="createProductSearch" oninput="filterDropdownList('create')"
                                   placeholder="Type product name to filter live..." 
                                   autocomplete="off"
                                   class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none">
                            <button type="button" onclick="clearDropdownSearch('create')" id="createProductSearchClear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Product Options List -->
                        <div id="createProductList" class="max-h-52 overflow-y-auto space-y-1 pr-1">
                            @foreach($products as $p)
                                <div class="product-option-item px-3 py-2 rounded-xl hover:bg-indigo-600/20 hover:text-white text-slate-300 text-xs cursor-pointer flex items-center justify-between transition-colors"
                                     data-id="{{ $p->id }}"
                                     data-name="{{ $p->name }}"
                                     data-unit="{{ $p->unit->short_code ?? ($p->unit->name ?? '') }}"
                                     onclick="selectDropdownProduct('create', '{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ addslashes($p->unit->short_code ?? ($p->unit->name ?? '')) }}')">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-[10px] text-cyan-400"></i>
                                        <span class="font-medium text-white">{{ $p->name }}</span>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-cyan-300 font-mono">
                                        {{ $p->unit->short_code ?? ($p->unit->name ?? 'Pcs') }}
                                    </span>
                                </div>
                            @endforeach
                            <!-- No Results Notice -->
                            <div id="createProductNoResults" class="hidden py-4 text-center text-xs text-slate-400">
                                <i class="fa-solid fa-triangle-exclamation text-amber-400 mb-1 block"></i>
                                No matching products found.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full Address -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Full Address
                    </label>
                    <textarea name="address" rows="2.5" placeholder="Factory / Office #, Road, Area, City"
                              class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Notes / Remarks (Optional)
                    </label>
                    <input type="text" name="notes" placeholder="e.g. Lead time 3 days, Quality Grade A"
                           value="{{ old('notes') }}"
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Supplier & Product</span>
                </button>
            </form>
        </div>

        <!-- Right: Suppliers Directory Table with Live Search & Load More -->
        <div class="lg:col-span-8 glass-panel rounded-3xl p-5 border border-slate-700/60">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3.5 pb-3 border-b border-slate-700/70">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-400"></i>
                        <span>Supplier & Purchase Records</span>
                    </h3>
                    <span id="supplierCountDisplay" class="text-[11px] text-cyan-300 font-mono">
                        Showing <span id="visibleSupplierCount">{{ min(10, $suppliers->count()) }}</span> of <span id="totalSupplierCount">{{ $suppliers->count() }}</span> Suppliers
                    </span>
                </div>

                <!-- Live Search Box (Supplier Name, Phone, Address, Product Name) -->
                <div class="relative w-full sm:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="supplierSearchInput" placeholder="Live search by Name, Phone, Product, Address..." 
                           class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none transition-all">
                    <button type="button" id="clearSupplierSearchBtn" onclick="clearSupplierSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[720px]">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-3">SL</th>
                            <th class="py-3 px-3">Supplier Name</th>
                            <th class="py-3 px-3">Phone</th>
                            <th class="py-3 px-3">Purchased Product</th>
                            <th class="py-3 px-3">Address</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="suppliersTableBody" class="divide-y divide-slate-800/80">
                        @forelse($suppliers as $index => $supplier)
                        <tr class="supplier-row hover:bg-slate-800/40 transition-colors"
                            data-search="{{ strtolower($supplier->name . ' ' . $supplier->phone . ' ' . ($supplier->address ?? '') . ' ' . ($supplier->product->name ?? '') . ' ' . ($supplier->notes ?? '')) }}">
                            
                            <!-- SL -->
                            <td class="py-3.5 px-3 font-mono text-slate-400 whitespace-nowrap">
                                #{{ $loop->iteration }}
                            </td>

                            <!-- Supplier Name -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-[11px] border border-indigo-500/20">
                                        <i class="fa-solid fa-building"></i>
                                    </span>
                                    <div>
                                        <span class="font-bold text-white block">{{ $supplier->name }}</span>
                                        @if($supplier->notes)
                                        <span class="text-[10px] text-slate-400 block truncate max-w-[180px]" title="{{ $supplier->notes }}">
                                            {{ $supplier->notes }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Phone -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <a href="tel:{{ $supplier->phone }}" class="inline-flex items-center gap-1.5 text-cyan-300 hover:text-cyan-200 font-mono text-xs">
                                    <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                                    <span>{{ $supplier->phone }}</span>
                                </a>
                            </td>

                            <!-- Purchased Product (Dropdown Linked) -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @if($supplier->product)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-gradient-to-r from-cyan-950/60 to-blue-950/60 border border-cyan-500/30 text-cyan-300">
                                    <i class="fa-solid fa-box text-[10px] text-cyan-400"></i>
                                    <span class="font-semibold">{{ $supplier->product->name }}</span>
                                    @if($supplier->product->unit)
                                    <span class="px-1.5 py-0.2 rounded bg-black/40 text-[10px] text-slate-400 font-mono">
                                        {{ $supplier->product->unit->short_code ?? $supplier->product->unit->name }}
                                    </span>
                                    @endif
                                </div>
                                @else
                                <span class="text-slate-500 italic">Not Assigned</span>
                                @endif
                            </td>

                            <!-- Address -->
                            <td class="py-3.5 px-3 text-slate-300 max-w-[200px]">
                                <div class="flex items-start gap-1.5">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-[11px] mt-0.5"></i>
                                    <span class="truncate" title="{{ $supplier->address ?? 'N/A' }}">
                                        {{ $supplier->address ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            onclick="openEditSupplierModal(
                                                '{{ $supplier->id }}',
                                                '{{ addslashes($supplier->name) }}',
                                                '{{ addslashes($supplier->phone) }}',
                                                '{{ addslashes($supplier->address ?? '') }}',
                                                '{{ $supplier->product_id ?? '' }}',
                                                '{{ addslashes($supplier->product->name ?? '') }}',
                                                '{{ addslashes($supplier->product->unit->short_code ?? ($supplier->product->unit->name ?? '')) }}',
                                                '{{ addslashes($supplier->notes ?? '') }}'
                                            )"
                                            class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-indigo-600/30 text-indigo-300 border border-slate-700 hover:border-indigo-500/40 font-semibold text-[11px] transition-colors inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('factory.suppliers.destroy', $supplier->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete supplier {{ addslashes($supplier->name) }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-rose-600/30 text-rose-400 hover:text-rose-300 border border-slate-700 hover:border-rose-500/40 font-semibold text-[11px] transition-colors inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-truck-ramp-box text-3xl text-slate-600"></i>
                                    <span class="text-slate-400 font-medium">No suppliers registered yet.</span>
                                    <span class="text-xs text-slate-500">Fill out the form on the left to add your first supplier.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse

                        <!-- No Search Results Row -->
                        <tr id="noSupplierSearchResultRow" class="hidden">
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-magnifying-glass text-2xl text-slate-600"></i>
                                    <span class="text-xs">No suppliers match your search.</span>
                                    <button type="button" onclick="clearSupplierSearch()" class="text-indigo-400 text-xs underline mt-1">Clear search</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Load More Suppliers Button -->
            <div id="loadMoreSuppliersContainer" class="mt-4 pt-3 border-t border-slate-800 text-center {{ $suppliers->count() > 10 ? '' : 'hidden' }}">
                <button type="button" onclick="loadMoreSuppliers()"
                        class="px-5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-indigo-300 hover:text-white border border-slate-700 hover:border-indigo-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-angles-down text-indigo-400"></i>
                    <span>Load More Suppliers (+10)</span>
                </button>
            </div>

        </div>

    </div>

</div>

<!-- EDIT SUPPLIER MODAL -->
<div id="editSupplierModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-indigo-400"></i>
                <span>Edit Supplier Details</span>
            </h3>
            <button type="button" onclick="closeEditSupplierModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editSupplierForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <!-- Supplier Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Supplier Name *</label>
                <input type="text" name="name" id="editSupplierName" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-indigo-400 focus:outline-none">
            </div>

            <!-- Phone Number -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                <input type="text" name="phone" id="editSupplierPhone" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-indigo-400 focus:outline-none">
            </div>

            <!-- Product Selector with Live Search -->
            <div class="relative">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Purchased Product Name *</label>
                
                <input type="hidden" name="product_id" id="editProductId" required>

                <!-- Trigger Button -->
                <button type="button" id="editProductDropdownBtn" onclick="toggleDropdown('edit')"
                        class="w-full bg-[#070d1e] text-xs text-left rounded-xl px-3.5 py-2.5 border border-slate-700 hover:border-indigo-400 focus:outline-none flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <i class="fa-solid fa-box text-cyan-400 text-xs flex-shrink-0"></i>
                        <span id="editProductLabel" class="text-white truncate">
                            -- Select Product --
                        </span>
                    </div>
                    <i id="editProductChevron" class="fa-solid fa-chevron-down text-slate-400 text-[11px] transition-transform"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="editProductDropdownMenu" 
                     class="hidden absolute z-30 left-0 right-0 top-full mt-1.5 rounded-2xl bg-[#0a1228]/95 border border-slate-700/80 shadow-2xl backdrop-blur-md p-2 space-y-2">
                    
                    <!-- Live Search Input -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="editProductSearch" oninput="filterDropdownList('edit')"
                               placeholder="Type product name to filter live..." 
                               autocomplete="off"
                               class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none">
                        <button type="button" onclick="clearDropdownSearch('edit')" id="editProductSearchClear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Products List -->
                    <div id="editProductList" class="max-h-48 overflow-y-auto space-y-1 pr-1">
                        @foreach($products as $p)
                            <div class="product-option-item px-3 py-2 rounded-xl hover:bg-indigo-600/20 hover:text-white text-slate-300 text-xs cursor-pointer flex items-center justify-between transition-colors"
                                 data-id="{{ $p->id }}"
                                 data-name="{{ $p->name }}"
                                 data-unit="{{ $p->unit->short_code ?? ($p->unit->name ?? '') }}"
                                 onclick="selectDropdownProduct('edit', '{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ addslashes($p->unit->short_code ?? ($p->unit->name ?? '')) }}')">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-tag text-[10px] text-cyan-400"></i>
                                    <span class="font-medium text-white">{{ $p->name }}</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-cyan-300 font-mono">
                                    {{ $p->unit->short_code ?? ($p->unit->name ?? 'Pcs') }}
                                </span>
                            </div>
                        @endforeach
                        <div id="editProductNoResults" class="hidden py-4 text-center text-xs text-slate-400">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400 mb-1 block"></i>
                            No matching products found.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Full Address</label>
                <textarea name="address" id="editSupplierAddress" rows="2.5"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none"></textarea>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Notes / Remarks</label>
                <input type="text" name="notes" id="editSupplierNotes"
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-indigo-400 focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditSupplierModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 hover:from-indigo-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Supplier</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // ==========================================
    // 1. PRODUCT SEARCHABLE DROPDOWN WITH LIVE SEARCH
    // ==========================================
    function toggleDropdown(mode) {
        const menu = document.getElementById(`${mode}ProductDropdownMenu`);
        const chevron = document.getElementById(`${mode}ProductChevron`);
        const searchInput = document.getElementById(`${mode}ProductSearch`);
        
        const isOpen = !menu.classList.contains('hidden');
        if (isOpen) {
            menu.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        } else {
            // Close other mode dropdown if open
            const otherMode = mode === 'create' ? 'edit' : 'create';
            const otherMenu = document.getElementById(`${otherMode}ProductDropdownMenu`);
            if (otherMenu) otherMenu.classList.add('hidden');

            menu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            setTimeout(() => searchInput.focus(), 50);
        }
    }

    function filterDropdownList(mode) {
        const searchVal = document.getElementById(`${mode}ProductSearch`).value.toLowerCase().trim();
        const clearBtn = document.getElementById(`${mode}ProductSearchClear`);
        clearBtn.classList.toggle('hidden', searchVal === '');

        const container = document.getElementById(`${mode}ProductList`);
        const items = container.querySelectorAll('.product-option-item');
        let matched = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const unit = (item.getAttribute('data-unit') || '').toLowerCase();
            if (name.includes(searchVal) || unit.includes(searchVal)) {
                item.classList.remove('hidden');
                matched++;
            } else {
                item.classList.add('hidden');
            }
        });

        const noResults = document.getElementById(`${mode}ProductNoResults`);
        if (noResults) {
            noResults.classList.toggle('hidden', matched > 0);
        }
    }

    function clearDropdownSearch(mode) {
        const searchInput = document.getElementById(`${mode}ProductSearch`);
        searchInput.value = '';
        filterDropdownList(mode);
        searchInput.focus();
    }

    function selectDropdownProduct(mode, id, name, unit) {
        document.getElementById(`${mode}ProductId`).value = id;
        
        const label = document.getElementById(`${mode}ProductLabel`);
        label.innerHTML = `<span class="text-white font-bold">${name}</span>` + 
                          (unit ? ` <span class="px-1.5 py-0.5 rounded bg-slate-800 text-cyan-300 font-mono text-[10px] ml-1.5">${unit}</span>` : '');
        label.classList.remove('text-slate-400');

        // Close dropdown
        const menu = document.getElementById(`${mode}ProductDropdownMenu`);
        const chevron = document.getElementById(`${mode}ProductChevron`);
        menu.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        ['create', 'edit'].forEach(mode => {
            const btn = document.getElementById(`${mode}ProductDropdownBtn`);
            const menu = document.getElementById(`${mode}ProductDropdownMenu`);
            if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                const chevron = document.getElementById(`${mode}ProductChevron`);
                if (chevron) chevron.classList.remove('rotate-180');
            }
        });
    });

    // ==========================================
    // 2. SUPPLIERS TABLE LIVE SEARCH & LOAD MORE
    // ==========================================
    let allSupplierRows = [];
    let supplierVisibleLimit = 10;
    const batchSize = 10;

    document.addEventListener('DOMContentLoaded', () => {
        allSupplierRows = Array.from(document.querySelectorAll('.supplier-row'));
        applySupplierVisibility();

        const searchInput = document.getElementById('supplierSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                document.getElementById('clearSupplierSearchBtn').classList.toggle('hidden', query === '');
                filterSuppliersByQuery(query);
            });
        }

        // Restore old product selection on validation failure if exists
        const oldProdId = document.getElementById('createProductId').value;
        if (oldProdId) {
            const matchedItem = document.querySelector(`#createProductList .product-option-item[data-id="${oldProdId}"]`);
            if (matchedItem) {
                selectDropdownProduct('create', oldProdId, matchedItem.getAttribute('data-name'), matchedItem.getAttribute('data-unit'));
            }
        }
    });

    function applySupplierVisibility() {
        allSupplierRows.forEach((row, index) => {
            if (index < supplierVisibleLimit) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const visibleCount = Math.min(supplierVisibleLimit, allSupplierRows.length);
        const visibleEl = document.getElementById('visibleSupplierCount');
        if (visibleEl) visibleEl.innerText = visibleCount;

        const loadMoreContainer = document.getElementById('loadMoreSuppliersContainer');
        if (loadMoreContainer) {
            loadMoreContainer.classList.toggle('hidden', visibleCount >= allSupplierRows.length);
        }
    }

    function loadMoreSuppliers() {
        supplierVisibleLimit += batchSize;
        applySupplierVisibility();
    }

    function filterSuppliersByQuery(query) {
        if (!query) {
            document.getElementById('noSupplierSearchResultRow').classList.add('hidden');
            applySupplierVisibility();
            return;
        }

        let matched = 0;
        allSupplierRows.forEach(row => {
            const text = (row.getAttribute('data-search') || '').toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noSupplierSearchResultRow').classList.toggle('hidden', matched > 0);
        document.getElementById('loadMoreSuppliersContainer').classList.add('hidden');
        document.getElementById('visibleSupplierCount').innerText = matched;
    }

    function clearSupplierSearch() {
        const searchInput = document.getElementById('supplierSearchInput');
        if (searchInput) {
            searchInput.value = '';
            document.getElementById('clearSupplierSearchBtn').classList.add('hidden');
            document.getElementById('noSupplierSearchResultRow').classList.add('hidden');
            applySupplierVisibility();
            searchInput.focus();
        }
    }

    // ==========================================
    // 3. EDIT SUPPLIER MODAL HANDLERS
    // ==========================================
    function openEditSupplierModal(id, name, phone, address, productId, productName, productUnit, notes) {
        document.getElementById('editSupplierName').value = name;
        document.getElementById('editSupplierPhone').value = phone;
        document.getElementById('editSupplierAddress').value = address || '';
        document.getElementById('editSupplierNotes').value = notes || '';
        document.getElementById('editSupplierForm').action = `/factory/suppliers/${id}`;

        if (productId) {
            selectDropdownProduct('edit', productId, productName, productUnit);
        } else {
            document.getElementById('editProductId').value = '';
            document.getElementById('editProductLabel').innerHTML = '<span class="text-slate-400">-- Select Product --</span>';
        }

        document.getElementById('editSupplierModal').classList.remove('hidden');
    }

    function closeEditSupplierModal() {
        document.getElementById('editSupplierModal').classList.add('hidden');
    }

    // Keyboard Accessibility (ESC to close)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeEditSupplierModal();
            ['create', 'edit'].forEach(mode => {
                const menu = document.getElementById(`${mode}ProductDropdownMenu`);
                if (menu) menu.classList.add('hidden');
                const chevron = document.getElementById(`${mode}ProductChevron`);
                if (chevron) chevron.classList.remove('rotate-180');
            });
        }
    });
</script>
@endpush
@endsection

