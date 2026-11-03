<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use App\Models\HrEmployee;
use App\Models\HrAttendance;
use App\Models\HrLeave;
use App\Models\HrPayroll;
use Carbon\Carbon;

class HrSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departments
        $deptCutting = HrDepartment::firstOrCreate(['name' => 'Cutting Department'], ['code' => 'CUT', 'description' => 'Fabric spreading, cutting, and sorting section']);
        $deptSewing = HrDepartment::firstOrCreate(['name' => 'Sewing Department'], ['code' => 'SEW', 'description' => 'Garment assembly, stitching, and line manufacturing']);
        $deptFinishing = HrDepartment::firstOrCreate(['name' => 'Finishing & Packing'], ['code' => 'FIN', 'description' => 'Ironing, buttoning, inspection, and carton packaging']);
        $deptQC = HrDepartment::firstOrCreate(['name' => 'Quality Control (QC)'], ['code' => 'QC', 'description' => 'AQL standard inspection, fabric and line quality assurance']);
        $deptStore = HrDepartment::firstOrCreate(['name' => 'Store & Inventory'], ['code' => 'STR', 'description' => 'Raw materials, trims, accessories, and finished goods warehouse']);
        $deptMaint = HrDepartment::firstOrCreate(['name' => 'Maintenance & Mechanics'], ['code' => 'MNT', 'description' => 'Sewing machine maintenance and factory electricals']);
        $deptHR = HrDepartment::firstOrCreate(['name' => 'HR, Admin & Accounts'], ['code' => 'HRA', 'description' => 'Human resources, compliance, payroll, and bookkeeping']);

        // 2. Designations
        $desCuttingMaster = HrDesignation::firstOrCreate(['title' => 'Cutting Master'], ['department_id' => $deptCutting->id, 'grade' => 'Grade-1', 'description' => 'Chief cutter & pattern maker']);
        $desSpreader = HrDesignation::firstOrCreate(['title' => 'Fabric Spreader'], ['department_id' => $deptCutting->id, 'grade' => 'Grade-4', 'description' => 'Spreading fabrics on cutting table']);

        $desSupervisor = HrDesignation::firstOrCreate(['title' => 'Line Supervisor'], ['department_id' => $deptSewing->id, 'grade' => 'Grade-2', 'description' => 'Production floor line controller']);
        $desSrOperator = HrDesignation::firstOrCreate(['title' => 'Senior Sewing Operator'], ['department_id' => $deptSewing->id, 'grade' => 'Grade-3', 'description' => 'Overlock / Flatlock machine operator']);
        $desJrOperator = HrDesignation::firstOrCreate(['title' => 'Junior Sewing Operator'], ['department_id' => $deptSewing->id, 'grade' => 'Grade-4', 'description' => 'Plain lockstitch operator']);
        $desHelper = HrDesignation::firstOrCreate(['title' => 'Floor Helper'], ['department_id' => $deptSewing->id, 'grade' => 'Grade-5', 'description' => 'Line assistance, thread trimmer']);

        $desIronMan = HrDesignation::firstOrCreate(['title' => 'Steam Iron Master'], ['department_id' => $deptFinishing->id, 'grade' => 'Grade-4', 'description' => 'Steam pressing and packing']);
        $desPacker = HrDesignation::firstOrCreate(['title' => 'Packing Operator'], ['department_id' => $deptFinishing->id, 'grade' => 'Grade-5', 'description' => 'Poly-pack and carton boxing']);

        $desQCInspector = HrDesignation::firstOrCreate(['title' => 'Quality Inspector'], ['department_id' => $deptQC->id, 'grade' => 'Grade-3', 'description' => 'End-line and roaming QC auditor']);
        $desStoreKeeper = HrDesignation::firstOrCreate(['title' => 'Store Officer'], ['department_id' => $deptStore->id, 'grade' => 'Grade-2', 'description' => 'Raw material and fabric custodian']);
        $desMechanic = HrDesignation::firstOrCreate(['title' => 'Chief Mechanic'], ['department_id' => $deptMaint->id, 'grade' => 'Grade-2', 'description' => 'Sewing machinery troubleshooter']);
        $desHROfficer = HrDesignation::firstOrCreate(['title' => 'HR & Compliance Officer'], ['department_id' => $deptHR->id, 'grade' => 'Grade-2', 'description' => 'Employee attendance and compliance management']);

        // 3. Employees
        $employeesData = [
            [
                'employee_code' => 'TBG-EMP-001',
                'name' => 'Md. Rafiqul Islam',
                'email' => 'rafiqul.cutting@turkeybangla.com',
                'phone' => '01711-234567',
                'emergency_phone' => '01811-987654',
                'nid' => '19852691234567890',
                'gender' => 'Male',
                'blood_group' => 'B+',
                'present_address' => 'Station Road, Tongi, Gazipur',
                'permanent_address' => 'Vill: Joypur, P.S: Trishal, Dist: Mymensingh',
                'department_id' => $deptCutting->id,
                'designation_id' => $desCuttingMaster->id,
                'joining_date' => '2023-01-15',
                'employment_type' => 'permanent',
                'shift' => 'General',
                'status' => 'active',
                'basic_salary' => 20000,
                'house_rent' => 8000,
                'medical_allowance' => 2000,
                'conveyance_allowance' => 2000,
                'gross_salary' => 32000,
                'payment_method' => 'Bank',
                'bank_name' => 'Dutch-Bangla Bank Ltd',
                'account_number' => '115.120.456891',
            ],
            [
                'employee_code' => 'TBG-EMP-002',
                'name' => 'Md. Aminul Haque',
                'email' => 'aminul.supervisor@turkeybangla.com',
                'phone' => '01822-345678',
                'emergency_phone' => '01922-876543',
                'nid' => '19882691234567891',
                'gender' => 'Male',
                'blood_group' => 'O+',
                'present_address' => 'Mill Gate, Tongi, Gazipur',
                'permanent_address' => 'Vill: Chandpur, Dist: Pabna',
                'department_id' => $deptSewing->id,
                'designation_id' => $desSupervisor->id,
                'joining_date' => '2023-03-01',
                'employment_type' => 'permanent',
                'shift' => 'Day',
                'status' => 'active',
                'basic_salary' => 16000,
                'house_rent' => 6000,
                'medical_allowance' => 1500,
                'conveyance_allowance' => 1500,
                'gross_salary' => 25000,
                'payment_method' => 'bKash',
                'bank_name' => 'bKash Merchant/Personal',
                'account_number' => '01822345678',
            ],
            [
                'employee_code' => 'TBG-EMP-003',
                'name' => 'Mst. Fatema Begum',
                'email' => null,
                'phone' => '01933-456789',
                'emergency_phone' => '01733-765432',
                'nid' => '19922691234567892',
                'gender' => 'Female',
                'blood_group' => 'A+',
                'present_address' => 'College Gate, Tongi, Gazipur',
                'permanent_address' => 'Vill: Kalampur, Dist: Jamalpur',
                'department_id' => $deptSewing->id,
                'designation_id' => $desSrOperator->id,
                'joining_date' => '2023-06-10',
                'employment_type' => 'permanent',
                'shift' => 'Day',
                'status' => 'active',
                'basic_salary' => 9500,
                'house_rent' => 3500,
                'medical_allowance' => 1000,
                'conveyance_allowance' => 1000,
                'gross_salary' => 15000,
                'payment_method' => 'Nagad',
                'bank_name' => 'Nagad Account',
                'account_number' => '01933456789',
            ],
            [
                'employee_code' => 'TBG-EMP-004',
                'name' => 'Nasima Akter',
                'email' => null,
                'phone' => '01644-567890',
                'emergency_phone' => '01744-654321',
                'nid' => '19952691234567893',
                'gender' => 'Female',
                'blood_group' => 'AB+',
                'present_address' => 'Bonomala Road, Tongi, Gazipur',
                'permanent_address' => 'Vill: Shibpur, Dist: Narsingdi',
                'department_id' => $deptSewing->id,
                'designation_id' => $desJrOperator->id,
                'joining_date' => '2024-01-05',
                'employment_type' => 'permanent',
                'shift' => 'Day',
                'status' => 'active',
                'basic_salary' => 8000,
                'house_rent' => 3000,
                'medical_allowance' => 750,
                'conveyance_allowance' => 750,
                'gross_salary' => 12500,
                'payment_method' => 'Cash',
                'bank_name' => null,
                'account_number' => null,
            ],
            [
                'employee_code' => 'TBG-EMP-005',
                'name' => 'Md. Shakil Ahmed',
                'email' => 'shakil.qc@turkeybangla.com',
                'phone' => '01755-678901',
                'emergency_phone' => '01855-543210',
                'nid' => '19902691234567894',
                'gender' => 'Male',
                'blood_group' => 'O-',
                'present_address' => 'Cherag Ali, Tongi, Gazipur',
                'permanent_address' => 'Vill: Dapunia, Dist: Bogura',
                'department_id' => $deptQC->id,
                'designation_id' => $desQCInspector->id,
                'joining_date' => '2023-08-20',
                'employment_type' => 'permanent',
                'shift' => 'General',
                'status' => 'active',
                'basic_salary' => 12000,
                'house_rent' => 5000,
                'medical_allowance' => 1500,
                'conveyance_allowance' => 1500,
                'gross_salary' => 20000,
                'payment_method' => 'Bank',
                'bank_name' => 'Islami Bank Bangladesh Ltd',
                'account_number' => '2050.145.8901',
            ],
            [
                'employee_code' => 'TBG-EMP-006',
                'name' => 'Sumon Chandra Das',
                'email' => null,
                'phone' => '01866-789012',
                'emergency_phone' => '01966-432109',
                'nid' => '19942691234567895',
                'gender' => 'Male',
                'blood_group' => 'B+',
                'present_address' => 'Dattapara, Tongi, Gazipur',
                'permanent_address' => 'Vill: Shatkhira Sadar, Dist: Shatkhira',
                'department_id' => $deptFinishing->id,
                'designation_id' => $desIronMan->id,
                'joining_date' => '2023-11-15',
                'employment_type' => 'permanent',
                'shift' => 'Day',
                'status' => 'active',
                'basic_salary' => 9000,
                'house_rent' => 3500,
                'medical_allowance' => 1000,
                'conveyance_allowance' => 1000,
                'gross_salary' => 14500,
                'payment_method' => 'bKash',
                'bank_name' => 'bKash Personal',
                'account_number' => '01866789012',
            ],
            [
                'employee_code' => 'TBG-EMP-007',
                'name' => 'Tanjila Sultana',
                'email' => 'tanjila.hr@turkeybangla.com',
                'phone' => '01777-890123',
                'emergency_phone' => '01877-321098',
                'nid' => '19932691234567896',
                'gender' => 'Female',
                'blood_group' => 'A-',
                'present_address' => 'Uttara Sector 11, Dhaka',
                'permanent_address' => 'Vill: Tangail Sadar, Dist: Tangail',
                'department_id' => $deptHR->id,
                'designation_id' => $desHROfficer->id,
                'joining_date' => '2023-04-01',
                'employment_type' => 'permanent',
                'shift' => 'General',
                'status' => 'active',
                'basic_salary' => 18000,
                'house_rent' => 7000,
                'medical_allowance' => 2500,
                'conveyance_allowance' => 2500,
                'gross_salary' => 30000,
                'payment_method' => 'Bank',
                'bank_name' => 'BRAC Bank Ltd',
                'account_number' => '1501.203.4910',
            ],
        ];

        foreach ($employeesData as $empData) {
            $employee = HrEmployee::updateOrCreate(['employee_code' => $empData['employee_code']], $empData);

            // 4. Sample Attendance for Today & Yesterday
            $today = Carbon::today()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');

            // Today attendance
            HrAttendance::firstOrCreate([
                'employee_id' => $employee->id,
                'date' => $today,
            ], [
                'check_in' => '08:00:00',
                'check_out' => '17:00:00',
                'status' => $employee->id == 4 ? 'late' : ($employee->id == 6 ? 'leave' : 'present'),
                'overtime_hours' => ($employee->id == 3 || $employee->id == 1) ? 2.5 : 0,
                'notes' => $employee->id == 4 ? 'Delayed due to traffic' : null,
            ]);

            // Yesterday attendance
            HrAttendance::firstOrCreate([
                'employee_id' => $employee->id,
                'date' => $yesterday,
            ], [
                'check_in' => '08:00:00',
                'check_out' => '19:00:00',
                'status' => 'present',
                'overtime_hours' => 2.0,
                'notes' => 'Regular work + 2 hrs OT',
            ]);

            // 5. Sample Leave for employee 6
            if ($employee->id == 6) {
                HrLeave::firstOrCreate([
                    'employee_id' => $employee->id,
                    'start_date' => $today,
                    'end_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                ], [
                    'leave_type' => 'sick',
                    'days_count' => 3,
                    'reason' => 'Viral fever and doctor recommended bed rest',
                    'status' => 'approved',
                    'approved_by' => 'Tanjila Sultana (HR)',
                ]);
            }

            // 6. Sample Payroll for last month (2026-10)
            $otHours = 26; // 26 hours total OT in the month
            $otRate = round(($employee->basic_salary / 208) * 2, 2); // Standard Bangladesh 2x OT rate per hour
            $otAmount = round($otHours * $otRate, 2);
            $bonus = 1000;
            $deductions = $employee->id == 4 ? 500 : 0;
            $netSalary = $employee->gross_salary + $otAmount + $bonus - $deductions;

            HrPayroll::firstOrCreate([
                'employee_id' => $employee->id,
                'month' => '2026-10',
            ], [
                'payroll_code' => 'TBG-PAY-202610-' . str_pad($employee->id, 3, '0', STR_PAD_LEFT),
                'basic_salary' => $employee->basic_salary,
                'allowances' => $employee->house_rent + $employee->medical_allowance + $employee->conveyance_allowance,
                'overtime_hours' => $otHours,
                'overtime_rate' => $otRate,
                'overtime_amount' => $otAmount,
                'bonus' => $bonus,
                'deductions' => $deductions,
                'net_salary' => $netSalary,
                'payment_status' => 'paid',
                'payment_date' => '2026-11-01',
                'payment_method' => $employee->payment_method,
                'transaction_ref' => 'TXN-' . rand(100000, 999999),
                'notes' => 'Salary of October 2026 disbursed successfully.',
            ]);
        }
    }
}

