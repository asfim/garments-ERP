<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HrEmployee;
use App\Models\HrAttendance;
use App\Models\HrPayroll;
use App\Models\HrAdvance;
use App\Models\HrLeave;
use App\Models\CompanySetting;
use Carbon\Carbon;

class EmployeePortalController extends Controller
{
    /**
     * Get the logged-in employee model
     */
    private function getCurrentEmployee()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // If user is directly linked to an employee
        if ($user->employee) {
            return $user->employee;
        }

        // If user is admin/staff testing, return first employee as fallback
        return HrEmployee::first();
    }

    /**
     * Dedicated Employee Dashboard
     */
    public function dashboard(Request $request)
    {
        $employee = $this->getCurrentEmployee();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No linked employee profile found.');
        }

        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'phone' => '+880 1712-345678'
        ]);

        $currentMonth = Carbon::now()->format('Y-m');

        // 1. Attendance stats for current month
        $attendances = HrAttendance::where('employee_id', $employee->id)
            ->where('date', 'LIKE', $currentMonth . '%')
            ->orderBy('date', 'desc')
            ->get();

        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $leaveDays = $attendances->where('status', 'leave')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $totalOvertimeHours = $attendances->sum('overtime_hours');

        // 2. Payroll / Salary Slips History
        $payrolls = HrPayroll::where('employee_id', $employee->id)
            ->orderBy('month', 'desc')
            ->get();

        $latestPayroll = $payrolls->first();

        // 3. Advance Salary & Loans History
        $advances = HrAdvance::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedAdvances = $advances->where('status', 'approved');
        $totalAdvanceTaken = $approvedAdvances->sum('amount');
        $totalAdvanceDeducted = $approvedAdvances->sum('deducted_amount');
        $outstandingAdvance = max(0, $totalAdvanceTaken - $totalAdvanceDeducted);

        // 4. Leave Records
        $leaves = HrLeave::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.dashboard', compact(
            'employee',
            'company',
            'currentMonth',
            'attendances',
            'presentDays',
            'lateDays',
            'leaveDays',
            'absentDays',
            'totalOvertimeHours',
            'payrolls',
            'latestPayroll',
            'advances',
            'totalAdvanceTaken',
            'totalAdvanceDeducted',
            'outstandingAdvance',
            'leaves'
        ));
    }

    /**
     * Submit an Advance Salary Request by Employee
     */
    public function requestAdvance(Request $request)
    {
        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100',
            'reason' => 'required|string|max:500',
            'repayment_months' => 'nullable|integer|min:1|max:12',
        ]);

        HrAdvance::create([
            'employee_id' => $employee->id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'repayment_months' => $request->repayment_months ?? 1,
            'status' => 'pending',
            'deducted_amount' => 0,
        ]);

        return back()->with('success', 'অগ্রিম বেতনের আবেদন সফলভাবে জমা দেওয়া হয়েছে (Advance request submitted successfully).');
    }

    /**
     * Submit a Leave Request by Employee
     */
    public function requestLeave(Request $request)
    {
        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        HrLeave::create([
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'ছুটির আবেদন সফলভাবে জমা দেওয়া হয়েছে (Leave application submitted successfully).');
    }
}

