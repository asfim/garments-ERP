<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use App\Models\HrEmployee;
use App\Models\HrAttendance;
use App\Models\HrLeave;
use App\Models\HrPayroll;
use App\Models\HrAdvance;
use App\Models\HrBiometricDevice;
use App\Models\HrBiometricLog;
use App\Services\ZkTecoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HrController extends Controller
{
    private function getCompany()
    {
        return CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka - 1710, Bangladesh',
            'phone' => '+880 1712-345678',
            'email' => 'hr@turkeybangla.com'
        ]);
    }

    // ==========================================
    // 1. HR DASHBOARD / OVERVIEW
    // ==========================================
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $currentMonth = Carbon::today()->format('Y-m');

        $totalEmployees = HrEmployee::count();
        $activeEmployees = HrEmployee::where('status', 'active')->count();
        $departmentsCount = HrDepartment::count();

        // Today attendance stats
        $attendancesToday = HrAttendance::whereDate('date', $today)->get();
        $presentToday = $attendancesToday->where('status', 'present')->count();
        $lateToday = $attendancesToday->where('status', 'late')->count();
        $leaveToday = $attendancesToday->where('status', 'leave')->count();
        $absentToday = $attendancesToday->where('status', 'absent')->count();
        $totalMarkedToday = $attendancesToday->count();
        $unmarkedToday = max(0, $activeEmployees - $totalMarkedToday);

        // Payroll stats for current month
        $currentPayroll = HrPayroll::where('month', $currentMonth)->get();
        $totalPayrollAmount = $currentPayroll->sum('net_salary');
        $paidPayrollCount = $currentPayroll->where('payment_status', 'paid')->count();
        $totalPayrollCount = $currentPayroll->count();

        // Recent attendances
        $recentAttendances = HrAttendance::with(['employee.department', 'employee.designation'])
            ->whereDate('date', $today)
            ->latest('updated_at')
            ->take(8)
            ->get();

        // Pending leaves
        $pendingLeaves = HrLeave::with(['employee.department', 'employee.designation'])
            ->where('status', 'pending')
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();

        // Department breakdown
        $departments = HrDepartment::withCount(['employees' => function($q) {
            $q->where('status', 'active');
        }])->get();

        return view('factory-erp.hr.index', compact(
            'totalEmployees',
            'activeEmployees',
            'departmentsCount',
            'presentToday',
            'lateToday',
            'leaveToday',
            'absentToday',
            'unmarkedToday',
            'totalMarkedToday',
            'totalPayrollAmount',
            'paidPayrollCount',
            'totalPayrollCount',
            'recentAttendances',
            'pendingLeaves',
            'departments',
            'today',
            'currentMonth'
        ));
    }

    // ==========================================
    // 2. EMPLOYEES MANAGEMENT
    // ==========================================
    public function employeesIndex(Request $request)
    {
        $query = HrEmployee::with(['department', 'designation']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('employee_code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('nid', 'like', "%{$s}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $employees = $query->orderBy('id', 'desc')->get();
        $departments = HrDepartment::with('designations')->orderBy('name')->get();
        $designations = HrDesignation::orderBy('title')->get();

        return view('factory-erp.hr.employees.index', compact('employees', 'departments', 'designations'));
    }

    public function employeesCreate()
    {
        $departments = HrDepartment::with('designations')->orderBy('name')->get();
        $designations = HrDesignation::orderBy('title')->get();
        $lastId = HrEmployee::max('id') ?? 0;
        $nextEmployeeCode = 'TBG-EMP-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        return view('factory-erp.hr.employees.create', compact('departments', 'designations', 'nextEmployeeCode'));
    }

    public function employeesStore(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'nullable|string|max:50|unique:hr_employees,employee_code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'emergency_phone' => 'nullable|string|max:50',
            'nid' => 'nullable|string|max:50',
            'gender' => 'required|string|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'department_id' => 'required|exists:hr_departments,id',
            'designation_id' => 'required|exists:hr_designations,id',
            'joining_date' => 'required|date',
            'employment_type' => 'required|string|in:permanent,contractual,daily,probation',
            'shift' => 'required|string|in:Day,Night,General',
            'status' => 'required|string|in:active,inactive,resigned,terminated',
            'basic_salary' => 'required|numeric|min:0',
            'house_rent' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'device_user_id' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $validated['payment_method'] = $validated['payment_method'] ?? ($employee->payment_method ?? 'Cash');

        if (empty($validated['employee_code'])) {
            $lastId = HrEmployee::max('id') ?? 0;
            $validated['employee_code'] = 'TBG-EMP-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        $validated['payment_method'] = $validated['payment_method'] ?? 'Cash';

        $basic = floatval($validated['basic_salary'] ?? 0);
        $house = floatval($validated['house_rent'] ?? 0);
        $med = floatval($validated['medical_allowance'] ?? 0);
        $conv = floatval($validated['conveyance_allowance'] ?? 0);
        $validated['gross_salary'] = $basic + $house + $med + $conv;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }

        HrEmployee::create($validated);

        return redirect()->route('factory.hr.employees.index')->with('success', 'Employee ' . $validated['name'] . ' registered successfully!');
    }

    public function employeesShow(Request $request, $id)
    {
        $employee = HrEmployee::with(['department', 'designation'])->findOrFail($id);

        // Attendance Calendar Filter (Year and Month)
        $year = intval($request->get('year', Carbon::now()->year));
        $month = intval($request->get('month', Carbon::now()->month));

        // Create month carbon instance
        $currentMonthDate = Carbon::createFromDate($year, $month, 1);
        $monthName = $currentMonthDate->format('F');
        $daysInMonth = $currentMonthDate->daysInMonth;
        $startDayOfWeek = $currentMonthDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

        // Query attendances for this month
        $attendances = HrAttendance::where('employee_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Attendance stats for this month
        $monthPresent = $attendances->where('status', 'present')->count();
        $monthLate = $attendances->where('status', 'late')->count();
        $monthLeave = $attendances->where('status', 'leave')->count();
        $monthAbsent = $attendances->where('status', 'absent')->count();
        $monthHalfDay = $attendances->where('status', 'half_day')->count();
        $monthTotalOtHours = $attendances->sum('overtime_hours');

        // Leaves history for this employee
        $leaves = HrLeave::where('employee_id', $id)->orderBy('start_date', 'desc')->get();
        $totalLeavesTaken = $leaves->where('status', 'approved')->sum('days_count');

        // Payroll & Salary history for this employee
        $payrolls = HrPayroll::where('employee_id', $id)->orderBy('month', 'desc')->get();
        $totalSalaryPaid = $payrolls->where('payment_status', 'paid')->sum('net_salary');
        $totalOtEarned = $payrolls->sum('overtime_amount');
        $totalDeductionsMade = $payrolls->sum('deductions');
        $totalAbsenceDeductions = $payrolls->sum('absence_deduction');
        $totalAdvanceDeductions = $payrolls->sum('advance_deduction');

        // Advance Salary & Loans history for this employee
        $advances = HrAdvance::where('employee_id', $id)->orderBy('advance_date', 'desc')->get();
        $totalAdvanceTaken = $advances->sum('amount');
        $totalAdvanceRecovered = $advances->where('status', 'recovered')->sum('amount');
        $totalAdvancePending = $advances->where('status', 'approved')->sum('amount');

        return view('factory-erp.hr.employees.show', compact(
            'employee',
            'year',
            'month',
            'monthName',
            'daysInMonth',
            'startDayOfWeek',
            'attendances',
            'monthPresent',
            'monthLate',
            'monthLeave',
            'monthAbsent',
            'monthHalfDay',
            'monthTotalOtHours',
            'leaves',
            'totalLeavesTaken',
            'payrolls',
            'totalSalaryPaid',
            'totalOtEarned',
            'totalDeductionsMade',
            'totalAbsenceDeductions',
            'totalAdvanceDeductions',
            'advances',
            'totalAdvanceTaken',
            'totalAdvanceRecovered',
            'totalAdvancePending'
        ));
    }

    public function getDesignationsByDepartment($departmentId)
    {
        $designations = HrDesignation::where('department_id', $departmentId)->orderBy('title')->get();
        return response()->json($designations);
    }

    public function employeesEdit($id)
    {
        $employee = HrEmployee::findOrFail($id);
        $departments = HrDepartment::with('designations')->orderBy('name')->get();
        $designations = HrDesignation::orderBy('title')->get();

        return view('factory-erp.hr.employees.edit', compact('employee', 'departments', 'designations'));
    }

    public function employeesUpdate(Request $request, $id)
    {
        $employee = HrEmployee::findOrFail($id);

        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:hr_employees,employee_code,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'emergency_phone' => 'nullable|string|max:50',
            'nid' => 'nullable|string|max:50',
            'gender' => 'required|string|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'department_id' => 'required|exists:hr_departments,id',
            'designation_id' => 'required|exists:hr_designations,id',
            'joining_date' => 'required|date',
            'employment_type' => 'required|string|in:permanent,contractual,daily,probation',
            'shift' => 'required|string|in:Day,Night,General',
            'status' => 'required|string|in:active,inactive,resigned,terminated',
            'basic_salary' => 'required|numeric|min:0',
            'house_rent' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'device_user_id' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $validated['payment_method'] = $validated['payment_method'] ?? ($employee->payment_method ?? 'Cash');

        $basic = floatval($validated['basic_salary'] ?? 0);
        $house = floatval($validated['house_rent'] ?? 0);
        $med = floatval($validated['medical_allowance'] ?? 0);
        $conv = floatval($validated['conveyance_allowance'] ?? 0);
        $validated['gross_salary'] = $basic + $house + $med + $conv;

        if ($request->hasFile('photo')) {
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }

        $employee->update($validated);

        return redirect()->route('factory.hr.employees.index')->with('success', 'Employee ' . $employee->name . ' updated successfully!');
    }

    public function employeesDestroy($id)
    {
        $employee = HrEmployee::findOrFail($id);
        if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
            Storage::disk('public')->delete($employee->photo);
        }
        $name = $employee->name;
        $employee->delete();

        return redirect()->back()->with('success', "Employee {$name} deleted successfully!");
    }

    public function employeesExportCsv()
    {
        $employees = HrEmployee::with(['department', 'designation'])->orderBy('id')->get();
        $fileName = 'Employees-List-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($employees) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Employee ID', 'Name', 'Phone', 'Email', 'Department', 'Designation', 'Joining Date', 'Type', 'Shift', 'Gross Salary', 'Payment Method', 'Status']);

            foreach ($employees as $emp) {
                fputcsv($handle, [
                    $emp->employee_code,
                    $emp->name,
                    $emp->phone,
                    $emp->email ?? 'N/A',
                    $emp->department->name ?? 'N/A',
                    $emp->designation->title ?? 'N/A',
                    $emp->joining_date ? $emp->joining_date->format('Y-m-d') : 'N/A',
                    ucfirst($emp->employment_type),
                    $emp->shift,
                    number_format($emp->gross_salary, 2),
                    $emp->payment_method,
                    ucfirst($emp->status)
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function employeesExportPdf()
    {
        $employees = HrEmployee::with(['department', 'designation'])->orderBy('id')->get();
        $company = $this->getCompany();

        $pdf = Pdf::loadView('factory-erp.hr.employees.pdf', compact('employees', 'company'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('Employees-List-' . date('Y-m-d') . '.pdf');
    }

    // ==========================================
    // 3. DEPARTMENTS & DESIGNATIONS
    // ==========================================
    public function departmentsIndex()
    {
        $departments = HrDepartment::with(['designations.employees', 'employees'])->orderBy('name')->get();
        return view('factory-erp.hr.departments.index', compact('departments'));
    }

    public function departmentsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_departments,name',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        HrDepartment::create($validated);
        return redirect()->back()->with('success', 'Department created successfully!');
    }

    public function departmentsUpdate(Request $request, $id)
    {
        $dept = HrDepartment::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_departments,name,' . $id,
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $dept->update($validated);
        return redirect()->back()->with('success', 'Department updated successfully!');
    }

    public function departmentsDestroy($id)
    {
        $dept = HrDepartment::findOrFail($id);
        $dept->delete();
        return redirect()->back()->with('success', 'Department deleted successfully!');
    }

    public function designationsIndex(Request $request)
    {
        $query = HrDesignation::with(['department', 'employees']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('grade', 'like', "%{$s}%");
            });
        }

        $designations = $query->orderBy('title')->get();
        $departments = HrDepartment::orderBy('name')->get();
        $totalDesignations = HrDesignation::count();
        $totalAssignedEmployees = HrEmployee::whereNotNull('designation_id')->count();
        $selectedDepartmentId = $request->get('department_id');

        return view('factory-erp.hr.designations.index', compact(
            'designations', 
            'departments', 
            'totalDesignations', 
            'totalAssignedEmployees',
            'selectedDepartmentId'
        ));
    }

    public function designationsStore(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:hr_departments,id',
            'title' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $des = HrDesignation::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'designation' => $des,
                'message' => 'Designation created successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Designation created successfully!');
    }

    public function designationsUpdate(Request $request, $id)
    {
        $des = HrDesignation::findOrFail($id);
        $validated = $request->validate([
            'department_id' => 'required|exists:hr_departments,id',
            'title' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $des->update($validated);
        return redirect()->back()->with('success', 'Designation updated successfully!');
    }

    public function designationsDestroy($id)
    {
        $des = HrDesignation::findOrFail($id);
        $des->delete();
        return redirect()->back()->with('success', 'Designation deleted successfully!');
    }

    // ==========================================
    // 4. DAILY ATTENDANCE MANAGEMENT
    // ==========================================
    public function attendanceIndex(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $deptId = $request->get('department_id');

        $empQuery = HrEmployee::with(['department', 'designation'])->where('status', 'active');
        if ($deptId) {
            $empQuery->where('department_id', $deptId);
        }
        $employees = $empQuery->orderBy('name')->get();

        // Load attendances for this date
        $attendances = HrAttendance::whereDate('date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        // Stats for this date
        $totalEmp = $employees->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $leaveCount = $attendances->where('status', 'leave')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $notMarkedCount = max(0, $totalEmp - $attendances->count());

        $departments = HrDepartment::orderBy('name')->get();

        return view('factory-erp.hr.attendance.index', compact(
            'employees',
            'attendances',
            'date',
            'deptId',
            'totalEmp',
            'presentCount',
            'lateCount',
            'leaveCount',
            'absentCount',
            'notMarkedCount',
            'departments'
        ));
    }

    public function attendanceStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'date' => 'required|date',
            'status' => 'required|string|in:present,absent,late,half_day,leave',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'overtime_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        HrAttendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            [
                'status' => $validated['status'],
                'check_in' => $validated['check_in'] ?? null,
                'check_out' => $validated['check_out'] ?? null,
                'overtime_hours' => floatval($validated['overtime_hours'] ?? 0),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Attendance recorded successfully!');
    }

    public function attendanceBulkStore(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $records = $request->input('attendance', []);

        if (empty($records)) {
            // "Mark All Unmarked as Present" quick action
            $activeEmployees = HrEmployee::where('status', 'active')->get();
            $existing = HrAttendance::whereDate('date', $date)->pluck('employee_id')->toArray();

            $markedCount = 0;
            foreach ($activeEmployees as $emp) {
                if (!in_array($emp->id, $existing)) {
                    HrAttendance::create([
                        'employee_id' => $emp->id,
                        'date' => $date,
                        'status' => 'present',
                        'check_in' => '08:00:00',
                        'check_out' => '17:00:00',
                        'overtime_hours' => 0,
                        'notes' => 'Bulk marked present'
                    ]);
                    $markedCount++;
                }
            }
            return redirect()->back()->with('success', "Marked {$markedCount} active employees as Present for {$date}!");
        }

        foreach ($records as $empId => $data) {
            HrAttendance::updateOrCreate(
                ['employee_id' => $empId, 'date' => $date],
                [
                    'status' => $data['status'] ?? 'present',
                    'check_in' => !empty($data['check_in']) ? $data['check_in'] : null,
                    'check_out' => !empty($data['check_out']) ? $data['check_out'] : null,
                    'overtime_hours' => floatval($data['overtime_hours'] ?? 0),
                    'notes' => $data['notes'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Bulk attendance updated successfully!');
    }

    public function attendanceExportCsv(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $employees = HrEmployee::with(['department', 'designation'])->where('status', 'active')->orderBy('name')->get();
        $attendances = HrAttendance::whereDate('date', $date)->get()->keyBy('employee_id');

        $fileName = 'Daily-Attendance-' . $date . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($employees, $attendances, $date) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date: ' . $date]);
            fputcsv($handle, ['Employee ID', 'Name', 'Department', 'Designation', 'Status', 'Check In', 'Check Out', 'OT (Hours)', 'Notes']);

            foreach ($employees as $emp) {
                $att = $attendances->get($emp->id);
                fputcsv($handle, [
                    $emp->employee_code,
                    $emp->name,
                    $emp->department->name ?? 'N/A',
                    $emp->designation->title ?? 'N/A',
                    $att ? strtoupper($att->status) : 'NOT MARKED',
                    $att && $att->check_in ? $att->check_in : '—',
                    $att && $att->check_out ? $att->check_out : '—',
                    $att ? $att->overtime_hours : 0,
                    $att && $att->notes ? $att->notes : ''
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function attendanceExportPdf(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $employees = HrEmployee::with(['department', 'designation'])->where('status', 'active')->orderBy('name')->get();
        $attendances = HrAttendance::whereDate('date', $date)->get()->keyBy('employee_id');
        $company = $this->getCompany();

        $pdf = Pdf::loadView('factory-erp.hr.attendance.pdf', compact('employees', 'attendances', 'date', 'company'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('Daily-Attendance-' . $date . '.pdf');
    }

    // ==========================================
    // 5. LEAVE MANAGEMENT
    // ==========================================
    public function leavesIndex(Request $request)
    {
        $query = HrLeave::with(['employee.department', 'employee.designation'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $leaves = $query->get();
        $employees = HrEmployee::where('status', 'active')->orderBy('name')->get();

        $pendingCount = HrLeave::where('status', 'pending')->count();
        $approvedCount = HrLeave::where('status', 'approved')->count();
        $rejectedCount = HrLeave::where('status', 'rejected')->count();

        return view('factory-erp.hr.leaves.index', compact('leaves', 'employees', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function leavesStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'leave_type' => 'required|string|in:casual,sick,earned,maternity,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysCount = $start->diffInDays($end) + 1;

        $validated['days_count'] = $daysCount;
        $validated['status'] = 'pending';

        HrLeave::create($validated);

        return redirect()->back()->with('success', 'Leave request submitted successfully!');
    }

    public function leavesUpdateStatus(Request $request, $id)
    {
        $leave = HrLeave::findOrFail($id);
        $action = $request->input('status'); // approved or rejected

        if (!in_array($action, ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'Invalid action!');
        }

        $leave->status = $action;
        $leave->approved_by = 'Admin (' . now()->format('d M, Y') . ')';
        $leave->save();

        return redirect()->back()->with('success', 'Leave application has been ' . ucfirst($action) . '!');
    }

    public function leavesDestroy($id)
    {
        $leave = HrLeave::findOrFail($id);
        $leave->delete();
        return redirect()->back()->with('success', 'Leave record deleted!');
    }

    // ==========================================
    // 5.1 ADVANCE SALARY & LOAN MANAGEMENT
    // ==========================================
    public function advancesIndex(Request $request)
    {
        $month = $request->get('month', Carbon::today()->format('Y-m'));
        $query = HrAdvance::with(['employee.department', 'employee.designation'])
            ->where('deduct_month', $month)
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $advances = $query->get();
        $employees = HrEmployee::where('status', 'active')->orderBy('name')->get();

        $totalAdvance = $advances->sum('amount');
        $approvedAdvance = $advances->where('status', 'approved')->sum('amount');
        $recoveredAdvance = $advances->where('status', 'recovered')->sum('amount');
        $pendingCount = $advances->where('status', 'pending')->count();

        return view('factory-erp.hr.advances.index', compact(
            'advances',
            'employees',
            'month',
            'totalAdvance',
            'approvedAdvance',
            'recoveredAdvance',
            'pendingCount'
        ));
    }

    public function advancesStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'deduct_month' => 'required|string',
            'payment_method' => 'required|string|in:Cash,Bank,bKash,Nagad',
            'reason' => 'nullable|string|max:255',
            'status' => 'required|string|in:approved,pending',
        ]);

        if ($validated['status'] === 'approved') {
            $validated['approved_by'] = 'Admin (' . now()->format('d M, Y') . ')';
        }

        $advance = HrAdvance::create($validated);

        // Sync with Accounts & Finance Module if approved
        if ($advance->status === 'approved') {
            app(\App\Services\AccountingService::class)->syncAdvancePayment($advance);
        }

        return redirect()->back()->with('success', 'Advance salary of ৳' . number_format($validated['amount'], 2) . ' recorded successfully!');
    }

    public function advancesUpdateStatus(Request $request, $id)
    {
        $advance = HrAdvance::findOrFail($id);
        $status = $request->input('status');

        if (!in_array($status, ['approved', 'recovered', 'rejected'])) {
            return redirect()->back()->with('error', 'Invalid status!');
        }

        $advance->status = $status;
        if ($status === 'approved') {
            $advance->approved_by = 'Admin (' . now()->format('d M, Y') . ')';
        }
        $advance->save();

        // Sync with Accounts & Finance Module
        app(\App\Services\AccountingService::class)->syncAdvancePayment($advance);

        return redirect()->back()->with('success', 'Advance salary request has been ' . ucfirst($status) . '!');
    }

    public function advancesDestroy($id)
    {
        $advance = HrAdvance::findOrFail($id);
        $advance->delete();
        return redirect()->back()->with('success', 'Advance record deleted!');
    }

    // ==========================================
    // 6. PAYROLL & PAYSLIPS
    // ==========================================
    public function payrollIndex(Request $request)
    {
        $month = $request->get('month', Carbon::today()->format('Y-m'));

        $payrolls = HrPayroll::with(['employee.department', 'employee.designation'])
            ->where('month', $month)
            ->latest('id')
            ->get();

        $totalBasic = $payrolls->sum('basic_salary');
        $totalOT = $payrolls->sum('overtime_amount');
        $totalBonus = $payrolls->sum('bonus');
        $totalDeductions = $payrolls->sum('deductions');
        $totalNetSalary = $payrolls->sum('net_salary');
        $paidCount = $payrolls->where('payment_status', 'paid')->count();
        $unpaidCount = $payrolls->where('payment_status', 'unpaid')->count();

        return view('factory-erp.hr.payroll.index', compact(
            'payrolls',
            'month',
            'totalBasic',
            'totalOT',
            'totalBonus',
            'totalDeductions',
            'totalNetSalary',
            'paidCount',
            'unpaidCount'
        ));
    }

    public function payrollGenerate(Request $request)
    {
        $month = $request->input('month', Carbon::today()->format('Y-m'));
        $employees = HrEmployee::where('status', 'active')->get();

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No active employees found to generate payroll!');
        }

        $yearMonth = explode('-', $month);
        $year = intval($yearMonth[0]);
        $mon = intval($yearMonth[1]);
        $daysInMonth = Carbon::createFromDate($year, $mon, 1)->daysInMonth;

        $count = 0;
        foreach ($employees as $emp) {
            // Calculate total overtime hours from attendance
            $otHours = HrAttendance::where('employee_id', $emp->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $mon)
                ->sum('overtime_hours');

            // Calculate absent days from attendance
            $absentDays = HrAttendance::where('employee_id', $emp->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $mon)
                ->where('status', 'absent')
                ->count();

            // OT calculation: (basic / 208) * 2
            $otRate = round(($emp->basic_salary / 208) * 2, 2);
            $otAmount = round($otHours * $otRate, 2);

            // Deductions for absent days: (gross / daysInMonth) * absentDays
            $absenceDeduction = round(($emp->gross_salary / $daysInMonth) * $absentDays, 2);

            // Deductions for advance salary approved for this month
            $advanceDeduction = HrAdvance::where('employee_id', $emp->id)
                ->where('deduct_month', $month)
                ->whereIn('status', ['approved', 'recovered'])
                ->sum('amount');

            $totalDeductions = round($absenceDeduction + $advanceDeduction, 2);
            $netSalary = max(0, round($emp->gross_salary + $otAmount - $totalDeductions, 2));
            $payrollCode = 'TBG-PAY-' . str_replace('-', '', $month) . '-' . str_pad($emp->id, 3, '0', STR_PAD_LEFT);

            HrPayroll::updateOrCreate(
                ['employee_id' => $emp->id, 'month' => $month],
                [
                    'payroll_code' => $payrollCode,
                    'basic_salary' => $emp->basic_salary,
                    'allowances' => ($emp->house_rent + $emp->medical_allowance + $emp->conveyance_allowance),
                    'overtime_hours' => $otHours,
                    'overtime_rate' => $otRate,
                    'overtime_amount' => $otAmount,
                    'bonus' => 0,
                    'absence_deduction' => $absenceDeduction,
                    'advance_deduction' => $advanceDeduction,
                    'penalty_deduction' => 0,
                    'deductions' => $totalDeductions,
                    'net_salary' => $netSalary,
                    'payment_method' => $emp->payment_method,
                ]
            );
            $count++;
        }

        return redirect()->back()->with('success', "Payroll for {$month} calculated & generated for {$count} active employees (with OT and Advance deductions)!");
    }

    public function payrollUpdatePayment(Request $request, $id)
    {
        $payroll = HrPayroll::findOrFail($id);
        $validated = $request->validate([
            'payment_status' => 'required|string|in:paid,unpaid',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'transaction_ref' => 'nullable|string|max:100',
            'bonus' => 'nullable|numeric|min:0',
            'absence_deduction' => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'penalty_deduction' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bonus = floatval($validated['bonus'] ?? $payroll->bonus);
        $absDed = floatval($validated['absence_deduction'] ?? $payroll->absence_deduction);
        $advDed = floatval($validated['advance_deduction'] ?? $payroll->advance_deduction);
        $penDed = floatval($validated['penalty_deduction'] ?? $payroll->penalty_deduction);
        
        $totalDeductions = ($validated['deductions'] !== null && $validated['deductions'] != $payroll->deductions) 
            ? floatval($validated['deductions']) 
            : ($absDed + $advDed + $penDed);

        $net = max(0, ($payroll->basic_salary + $payroll->allowances + $payroll->overtime_amount + $bonus) - $totalDeductions);

        $payroll->payment_status = $validated['payment_status'];
        $payroll->payment_date = $validated['payment_status'] === 'paid' ? ($validated['payment_date'] ?? now()->toDateString()) : null;
        $payroll->payment_method = $validated['payment_method'] ?? $payroll->payment_method;
        $payroll->transaction_ref = $validated['transaction_ref'] ?? null;
        $payroll->bonus = $bonus;
        $payroll->absence_deduction = $absDed;
        $payroll->advance_deduction = $advDed;
        $payroll->penalty_deduction = $penDed;
        $payroll->deductions = $totalDeductions;
        $payroll->net_salary = $net;
        $payroll->notes = $validated['notes'] ?? null;
        $payroll->save();

        // If marked paid, automatically mark all approved advances for that employee and month as 'recovered'
        if ($payroll->payment_status === 'paid') {
            HrAdvance::where('employee_id', $payroll->employee_id)
                ->where('deduct_month', $payroll->month)
                ->where('status', 'approved')
                ->update(['status' => 'recovered']);
        }
        $payroll->save();

        // Sync with Accounts & Finance Module
        app(\App\Services\AccountingService::class)->syncPayrollPayment($payroll);

        return redirect()->back()->with('success', 'Payroll record updated successfully!');
    }

    public function payrollPayslip($id)
    {
        $payroll = HrPayroll::with(['employee.department', 'employee.designation'])->findOrFail($id);
        $company = $this->getCompany();

        return view('factory-erp.hr.payroll.payslip', compact('payroll', 'company'));
    }

    public function payrollPayslipPdf($id)
    {
        $payroll = HrPayroll::with(['employee.department', 'employee.designation'])->findOrFail($id);
        $company = $this->getCompany();

        $pdf = Pdf::loadView('factory-erp.hr.payroll.payslip-pdf', compact('payroll', 'company'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download("Payslip-{$payroll->employee->employee_code}-{$payroll->month}.pdf");
    }

    public function payrollExportCsv(Request $request)
    {
        $month = $request->get('month', Carbon::today()->format('Y-m'));
        $payrolls = HrPayroll::with(['employee.department', 'employee.designation'])
            ->where('month', $month)
            ->latest('id')
            ->get();

        $fileName = 'Payroll-Sheet-' . $month . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($payrolls, $month) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Monthly Payroll Sheet: ' . $month]);
            fputcsv($handle, ['Slip No', 'Emp ID', 'Name', 'Department', 'Designation', 'Basic', 'Allowances', 'OT (Hrs)', 'OT Pay', 'Bonus', 'Deductions', 'Net Salary', 'Method', 'Status', 'Paid Date']);

            foreach ($payrolls as $p) {
                fputcsv($handle, [
                    $p->payroll_code,
                    $p->employee->employee_code ?? 'N/A',
                    $p->employee->name ?? 'N/A',
                    $p->employee->department->name ?? 'N/A',
                    $p->employee->designation->title ?? 'N/A',
                    number_format($p->basic_salary, 2),
                    number_format($p->allowances, 2),
                    $p->overtime_hours,
                    number_format($p->overtime_amount, 2),
                    number_format($p->bonus, 2),
                    number_format($p->deductions, 2),
                    number_format($p->net_salary, 2),
                    $p->payment_method ?? 'N/A',
                    strtoupper($p->payment_status),
                    $p->payment_date ? $p->payment_date->format('Y-m-d') : '—'
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function payrollExportPdf(Request $request)
    {
        $month = $request->get('month', Carbon::today()->format('Y-m'));
        $payrolls = HrPayroll::with(['employee.department', 'employee.designation'])
            ->where('month', $month)
            ->latest('id')
            ->get();
        $company = $this->getCompany();

        $pdf = Pdf::loadView('factory-erp.hr.payroll.pdf', compact('payrolls', 'month', 'company'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('Payroll-Sheet-' . $month . '.pdf');
    }

    // ==========================================
    // 7. BIOMETRIC DEVICES (ZKTECO)
    // ==========================================
    public function devicesIndex()
    {
        $devices = HrBiometricDevice::withCount('logs')->orderBy('id', 'asc')->get();
        $recentLogs = HrBiometricLog::with(['employee', 'device'])
            ->latest('punch_time')
            ->limit(100)
            ->get();
            
        $totalDevices = $devices->count();
        $activeDevices = $devices->where('is_active', true)->count();
        $totalLogsCount = HrBiometricLog::count();
        $biometricEmployeesCount = HrEmployee::whereNotNull('device_user_id')->count();
        $totalEmployees = HrEmployee::where('status', 'active')->count();

        return view('factory-erp.hr.devices.index', compact(
            'devices',
            'recentLogs',
            'totalDevices',
            'activeDevices',
            'totalLogsCount',
            'biometricEmployeesCount',
            'totalEmployees'
        ));
    }

    public function devicesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ip_address' => 'required|string|max:50',
            'port' => 'required|integer|min:1|max:65535',
            'model' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'protocol' => 'nullable|string|in:TCP/IP,UDP,ADMS',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'device_ip' => $validated['ip_address'],
            'port' => $validated['port'] ?? 4370,
            'device_model' => $validated['model'] ?? 'ZKTeco K40',
            'notes' => $validated['location'] ?? null,
            'protocol' => $validated['protocol'] ?? 'TCP/IP',
            'is_active' => $request->has('is_active') ? true : false,
            'status' => 'offline',
        ];

        HrBiometricDevice::create($data);

        return redirect()->route('factory.hr.devices.index')->with('success', 'Biometric device added successfully!');
    }

    public function devicesUpdate(Request $request, $id)
    {
        $device = HrBiometricDevice::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ip_address' => 'required|string|max:50',
            'port' => 'required|integer|min:1|max:65535',
            'model' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'protocol' => 'nullable|string|in:TCP/IP,UDP,ADMS',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'device_ip' => $validated['ip_address'],
            'port' => $validated['port'] ?? 4370,
            'device_model' => $validated['model'] ?? 'ZKTeco K40',
            'notes' => $validated['location'] ?? null,
            'protocol' => $validated['protocol'] ?? 'TCP/IP',
            'is_active' => $request->has('is_active') ? true : false,
        ];

        $device->update($data);

        return redirect()->back()->with('success', "Device '{$device->name}' updated successfully!");
    }

    public function devicesDestroy($id)
    {
        $device = HrBiometricDevice::findOrFail($id);
        $name = $device->name;
        $device->delete();

        return redirect()->back()->with('success', "Device '{$name}' deleted successfully!");
    }

    public function devicesTestConnection(Request $request, $id, ZkTecoService $zkService)
    {
        $device = HrBiometricDevice::findOrFail($id);
        $result = $zkService->testConnection($device->ip_address, $device->port, 3);

        if ($result['success']) {
            $device->update(['status' => 'online']);
            return response()->json([
                'success' => true,
                'latency' => $result['latency'],
                'message' => $result['message'],
            ]);
        } else {
            $device->update(['status' => 'offline']);
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }
    }

    public function devicesLiveSync(Request $request, $id, ZkTecoService $zkService)
    {
        $device = HrBiometricDevice::findOrFail($id);

        try {
            $logs = $zkService->fetchLiveLogs($device->ip_address, $device->port, 5);

            if (empty($logs)) {
                $device->update([
                    'status' => 'online',
                    'last_sync_at' => Carbon::now()
                ]);

                return redirect()->back()->with('info', "Connection to '{$device->name}' verified online. No unread punches found on device, or device is ready for ADMS/USB sync.");
            }

            $syncResult = $zkService->syncLogsToAttendance($logs, 'device_live', $device->id);

            $device->update([
                'status' => 'online',
                'last_sync_at' => Carbon::now()
            ]);

            return redirect()->back()->with('success', "Successfully synced {$syncResult['synced_punches']} punches! Updated attendance for {$syncResult['employees_updated']} employees across {$syncResult['affected_days']} days.");
        } catch (\Exception $e) {
            $device->update(['status' => 'offline']);
            return redirect()->back()->with('error', "Failed to sync with device: " . $e->getMessage());
        }
    }

    public function devicesAutoSync(Request $request, ZkTecoService $zkService)
    {
        $devices = HrBiometricDevice::where('is_active', true)->get();
        $totalSyncedPunches = 0;
        $totalEmployeesUpdated = 0;
        $syncedDevicesDetails = [];

        foreach ($devices as $device) {
            try {
                $conn = $zkService->testConnection($device->device_ip, $device->port, 2);
                if (!$conn['success']) {
                    $device->update(['status' => 'offline']);
                    $syncedDevicesDetails[] = [
                        'id' => $device->id,
                        'name' => $device->name,
                        'status' => 'offline',
                        'message' => 'Unreachable: ' . $device->device_ip
                    ];
                    continue;
                }

                $logs = $zkService->fetchLiveLogs($device->device_ip, $device->port, 3);
                $device->update([
                    'status' => 'online',
                    'last_sync_at' => Carbon::now()
                ]);

                if (!empty($logs)) {
                    $syncResult = $zkService->syncLogsToAttendance($logs, 'auto_sync', $device->id);
                    $totalSyncedPunches += $syncResult['synced_punches'];
                    $totalEmployeesUpdated += $syncResult['employees_updated'];
                }

                $syncedDevicesDetails[] = [
                    'id' => $device->id,
                    'name' => $device->name,
                    'status' => 'online',
                    'latency' => $conn['latency'],
                    'new_punches' => count($logs)
                ];
            } catch (\Exception $e) {
                $device->update(['status' => 'offline']);
                $syncedDevicesDetails[] = [
                    'id' => $device->id,
                    'name' => $device->name,
                    'status' => 'offline',
                    'message' => $e->getMessage()
                ];
            }
        }

        $recentLogs = HrBiometricLog::with(['employee.department', 'employee.designation', 'device'])
            ->latest('punch_time')
            ->limit(25)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'punch_time' => $log->punch_time ? $log->punch_time->format('d M, Y h:i:s A') : '—',
                    'device_user_id' => $log->device_user_id,
                    'employee_name' => $log->employee ? $log->employee->name : null,
                    'employee_code' => $log->employee ? $log->employee->employee_code : null,
                    'department' => $log->employee && $log->employee->department ? $log->employee->department->name : null,
                    'designation' => $log->employee && $log->employee->designation ? $log->employee->designation->title : null,
                    'device_name' => $log->device ? $log->device->name : 'ZKTeco',
                    'source' => $log->source,
                    'punch_type' => $log->punch_type,
                ];
            });

        return response()->json([
            'success' => true,
            'timestamp' => Carbon::now()->format('h:i:s A'),
            'total_synced_punches' => $totalSyncedPunches,
            'total_employees_updated' => $totalEmployeesUpdated,
            'devices' => $syncedDevicesDetails,
            'recent_logs' => $recentLogs
        ]);
    }

    public function devicesImportUsb(Request $request, ZkTecoService $zkService)
    {
        $request->validate([
            'log_file' => 'nullable|file|max:10240',
            'log_text' => 'nullable|string',
            'device_id' => 'nullable|exists:hr_biometric_devices,id',
        ]);

        $content = '';
        if ($request->hasFile('log_file')) {
            $content = file_get_contents($request->file('log_file')->getRealPath());
        } elseif ($request->filled('log_text')) {
            $content = $request->input('log_text');
        } else {
            return redirect()->back()->with('error', 'Please upload a ZKTeco attlog.dat file or paste attendance log text.');
        }

        $logs = $zkService->parseUsbLogFile($content);

        if (empty($logs)) {
            return redirect()->back()->with('error', 'No valid attendance records found in the uploaded file/text. Please ensure the file is from a ZKTeco device (attlog.dat, .txt, or .csv).');
        }

        $deviceId = $request->input('device_id') ? intval($request->input('device_id')) : null;
        $syncResult = $zkService->syncLogsToAttendance($logs, 'usb_import', $deviceId);

        if ($deviceId) {
            HrBiometricDevice::where('id', $deviceId)->update(['last_sync_at' => Carbon::now()]);
        }

        return redirect()->back()->with('success', "USB Log Imported Successfully! Synced {$syncResult['synced_punches']} punches. Generated/updated attendance for {$syncResult['employees_updated']} employees across {$syncResult['affected_days']} dates.");
    }

    public function devicesAdmsPush(Request $request, ZkTecoService $zkService)
    {
        if ($request->isMethod('GET')) {
            return response("OK", 200)->header('Content-Type', 'text/plain');
        }

        $rawContent = $request->getContent();
        if (!empty($rawContent)) {
            $sn = $request->get('SN');
            $device = $sn ? HrBiometricDevice::where('model', 'LIKE', "%{$sn}%")->orWhere('name', 'LIKE', "%{$sn}%")->first() : null;
            
            $logs = $zkService->parseUsbLogFile($rawContent);
            if (!empty($logs)) {
                $zkService->syncLogsToAttendance($logs, 'adms_push', $device ? $device->id : null);
                if ($device) {
                    $device->update(['status' => 'online', 'last_sync_at' => Carbon::now()]);
                }
            }
        }

        return response("OK", 200)->header('Content-Type', 'text/plain');
    }
}

