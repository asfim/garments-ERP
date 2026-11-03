@extends('layouts.app')

@section('title', 'Payroll & Salary Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i>
                Payroll & Salary Management (বেতন ও পে-রোল)
            </h2>
            <p class="text-xs text-slate-400">Automated salary sheet generation, overtime calculation, deductions, payslips, and disbursement</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Generate Payroll Button -->
            <form action="{{ route('factory.hr.payroll.generate') }}" method="POST" onsubmit="return confirm('Generate / Recalculate payroll for {{ $month }} based on active workers and attendance OT?')">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" 
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Generate Payroll ({{ $month }})</span>
                </button>
            </form>

            <!-- Download CSV -->
            <a href="{{ route('factory.hr.payroll.export.csv', ['month' => $month]) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-emerald-400 hover:text-emerald-300 border border-slate-700 hover:border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-sm"></i>
                <span>Download CSV</span>
            </a>

            <!-- Download PDF -->
            <a href="{{ route('factory.hr.payroll.export.pdf', ['month' => $month]) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-rose-400 hover:text-rose-300 border border-slate-700 hover:border-rose-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span>Download PDF</span>
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

    <!-- Month Selector -->
    <div class="glass-panel rounded-2xl p-4 border border-slate-700/60">
        <form method="GET" action="{{ route('factory.hr.payroll.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Select Payroll Month *</label>
                <input type="month" name="month" value="{{ $month }}" 
                       class="bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-400 font-mono">
            </div>

            <button type="submit" class="py-2 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Load Month</span>
            </button>

            <span class="text-xs text-slate-400 ml-auto hidden sm:block">
                Showing salary sheet for: <strong class="text-emerald-400 font-mono">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong>
            </span>
        </form>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
        <div class="glass-panel rounded-xl p-3 border border-emerald-500/30 text-center bg-emerald-500/5">
            <span class="text-[10px] uppercase font-bold text-emerald-400 block">Total Net Payable</span>
            <span class="text-lg sm:text-xl font-mono font-extrabold text-emerald-300 mt-1 block">৳{{ number_format($totalNetSalary, 2) }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-slate-700/60 text-center">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Basic Salaries</span>
            <span class="text-lg sm:text-xl font-mono font-extrabold text-white mt-1 block">৳{{ number_format($totalBasic, 2) }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-amber-500/30 text-center bg-amber-500/5">
            <span class="text-[10px] uppercase font-bold text-amber-400 block">Overtime Pay (OT)</span>
            <span class="text-lg sm:text-xl font-mono font-extrabold text-amber-300 mt-1 block">৳{{ number_format($totalOT, 2) }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-rose-500/30 text-center bg-rose-500/5">
            <span class="text-[10px] uppercase font-bold text-rose-400 block">Total Deductions</span>
            <span class="text-lg sm:text-xl font-mono font-extrabold text-rose-300 mt-1 block">৳{{ number_format($totalDeductions, 2) }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-cyan-500/30 text-center bg-cyan-500/5 col-span-2 sm:col-span-1">
            <span class="text-[10px] uppercase font-bold text-cyan-400 block">Disbursed / Paid</span>
            <span class="text-lg sm:text-xl font-mono font-extrabold text-cyan-300 mt-1 block">{{ $paidCount }} / {{ $payrolls->count() }}</span>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
        <div class="p-4 border-b border-slate-700/60 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-ol text-emerald-400"></i>
                Salary Slips for <span class="font-mono text-cyan-300">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</span>
                <span class="text-xs text-slate-400 font-normal">({{ $payrolls->count() }} Staff)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                    <tr>
                        <th class="py-3 px-3">Slip ID</th>
                        <th class="py-3 px-3">Worker Name & Section</th>
                        <th class="py-3 px-3 text-right">Basic</th>
                        <th class="py-3 px-3 text-right">Allowances</th>
                        <th class="py-3 px-3 text-center">OT (Hrs)</th>
                        <th class="py-3 px-3 text-right">OT Pay</th>
                        <th class="py-3 px-3 text-right">Deductions</th>
                        <th class="py-3 px-3 text-right">Net Payable</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($payrolls as $p)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-3 font-mono font-bold text-cyan-400">
                            {{ $p->payroll_code }}
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-bold text-white">{{ $p->employee->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400">
                                <span class="font-mono text-cyan-400">{{ $p->employee->employee_code ?? '' }}</span> • 
                                {{ $p->employee->department->name ?? 'Floor' }} ({{ $p->employee->designation->title ?? '' }})
                            </div>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-300">
                            ৳{{ number_format($p->basic_salary, 2) }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-300">
                            ৳{{ number_format($p->allowances, 2) }}
                        </td>
                        <td class="py-3 px-3 text-center font-mono font-bold {{ $p->overtime_hours > 0 ? 'text-amber-400' : 'text-slate-500' }}">
                            {{ $p->overtime_hours }}h
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-amber-300">
                            ৳{{ number_format($p->overtime_amount, 2) }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold {{ $p->deductions > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                            ৳{{ number_format($p->deductions, 2) }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-extrabold text-emerald-400 text-sm">
                            ৳{{ number_format($p->net_salary, 2) }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($p->payment_status === 'paid')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    PAID
                                </span>
                                @if($p->payment_date)
                                    <div class="text-[9px] text-slate-400 font-mono mt-0.5">{{ $p->payment_date->format('d M') }}</div>
                                @endif
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    UNPAID
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Update Payment Status -->
                                <button type="button" onclick="openPaymentModal({{ json_encode($p) }})" title="Update Payment Status" 
                                        class="p-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs transition-colors">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                </button>

                                <!-- View / Print Payslip -->
                                <a href="{{ route('factory.hr.payroll.payslip', $p->id) }}" title="View Printable Payslip" 
                                   class="p-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 text-xs transition-colors">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>

                                <!-- Download Payslip PDF -->
                                <a href="{{ route('factory.hr.payroll.payslip.pdf', $p->id) }}" title="Download Payslip PDF" 
                                   class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs transition-colors">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-10 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-file-invoice-dollar text-3xl text-slate-500 mb-2 block"></i>
                            No payroll generated for <strong class="text-white">{{ $month }}</strong> yet.
                            <div class="mt-2">
                                <form action="{{ route('factory.hr.payroll.generate') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all inline-flex items-center gap-2">
                                        <i class="fa-solid fa-calculator"></i>
                                        <span>Click here to Generate Payroll</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Update Payment Status -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-emerald-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-400"></i>
                Disburse / Pay Salary (<span id="payEmpName" class="text-cyan-300"></span>)
            </h3>
            <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="paymentForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Status *</label>
                <select name="payment_status" id="payStatus" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-400 font-bold">
                    <option value="paid">PAID (পরিশোধিত)</option>
                    <option value="unpaid">UNPAID (বকেয়া)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Date</label>
                    <input type="date" name="payment_date" id="payDate" value="{{ date('Y-m-d') }}" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Method</label>
                    <select name="payment_method" id="payMethod" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-400">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank Account</option>
                        <option value="bKash">bKash</option>
                        <option value="Nagad">Nagad</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Bonus (৳)</label>
                    <input type="number" step="0.01" name="bonus" id="payBonus" value="0" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Absence Ded. (৳)</label>
                    <input type="number" step="0.01" name="absence_deduction" id="payAbsDed" value="0" oninput="recalcDeductions()" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono focus:outline-none focus:border-rose-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Advance Ded. (৳)</label>
                    <input type="number" step="0.01" name="advance_deduction" id="payAdvDed" value="0" oninput="recalcDeductions()" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono focus:outline-none focus:border-rose-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Penalty / Fine (৳)</label>
                    <input type="number" step="0.01" name="penalty_deduction" id="payPenDed" value="0" oninput="recalcDeductions()" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono focus:outline-none focus:border-rose-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Total Deductions (৳)</label>
                <input type="number" step="0.01" name="deductions" id="payDeduction" value="0" class="w-full bg-[#050b1c] border border-rose-500/40 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono font-bold focus:outline-none focus:border-rose-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Transaction Ref / Slip No</label>
                <input type="text" name="transaction_ref" id="payRef" placeholder="e.g. TXN-123456 / Cheque #..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Disbursement Notes</label>
                <textarea name="notes" id="payNotes" rows="2" placeholder="Note..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">Save Payment</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function recalcDeductions() {
        const abs = parseFloat(document.getElementById('payAbsDed').value) || 0;
        const adv = parseFloat(document.getElementById('payAdvDed').value) || 0;
        const pen = parseFloat(document.getElementById('payPenDed').value) || 0;
        document.getElementById('payDeduction').value = (abs + adv + pen).toFixed(2);
    }

    function openPaymentModal(payroll) {
        document.getElementById('paymentForm').action = "/factory/hr/payroll/" + payroll.id + "/payment";
        document.getElementById('payEmpName').innerText = (payroll.employee ? payroll.employee.name : 'Worker') + ' (' + (payroll.employee ? payroll.employee.employee_code : '') + ')';
        document.getElementById('payStatus').value = payroll.payment_status || 'paid';
        document.getElementById('payDate').value = payroll.payment_date ? payroll.payment_date.substring(0, 10) : '{{ date('Y-m-d') }}';
        document.getElementById('payMethod').value = payroll.payment_method || (payroll.employee ? payroll.employee.payment_method : 'Cash');
        document.getElementById('payBonus').value = payroll.bonus || 0;
        document.getElementById('payAbsDed').value = payroll.absence_deduction || 0;
        document.getElementById('payAdvDed').value = payroll.advance_deduction || 0;
        document.getElementById('payPenDed').value = payroll.penalty_deduction || 0;
        document.getElementById('payDeduction').value = payroll.deductions || 0;
        document.getElementById('payRef').value = payroll.transaction_ref || '';
        document.getElementById('payNotes').value = payroll.notes || '';
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

