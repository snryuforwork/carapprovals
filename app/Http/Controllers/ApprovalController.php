<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Approval;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApprovalExport;
use Illuminate\Support\Facades\Mail; 
use App\Mail\ApprovalMail;           

class ApprovalController extends Controller
{
    /**
     * 1. รายการทั้งหมด (แสดงเฉพาะเวอร์ชันล่าสุดของแต่ละกลุ่ม)
     */
    public function index(Request $request)
        {
            $user = auth()->user();
            $isSale = $user->role === 'sale';
            $isAdmin = $user->role === 'admin';

            $sort = $request->input('sort', 'newest');
            
            // 1. ดึงข้อมูลทั้งหมดมาก่อน
            $query = Approval::select('approvals.*', 'users.name as sales_name')
                ->join(
                    DB::raw('(SELECT group_id, MAX(version) as max_version FROM approvals GROUP BY group_id) latest'),
                    function ($join) {
                        $join->on('approvals.group_id', '=', 'latest.group_id')
                            ->on('approvals.version', '=', 'latest.max_version');
                    }
                )
                ->leftJoin('users', 'users.id', '=', 'approvals.sales_user_id');

            if ($request->filled('sales_user_id')) {
                $query->where('approvals.sales_user_id', $request->input('sales_user_id'));
            }
            if ($request->filled('status')) {
                $query->where('approvals.status', $request->input('status'));
            }


            $allApprovals = $query->orderBy('approvals.updated_at', ($sort === 'oldest' ? 'ASC' : 'DESC'))->get();

            // 2. แยกตารางที่ Controller เลย (ตัดปัญหา Logic ใน View ตีกัน)
            
            // ตารางบน: Admin/Sale เห็นเหมือนกันคือ Waiting และ Approved
            $mainApprovals = $allApprovals->filter(function ($val) {
                return in_array($val->status, ['Waiting', 'Approved']);
            });

            // ตารางล่าง:
            if ($isSale) {
                // Sale เห็นทั้ง Draft และ Reject
                $draftApprovals = $allApprovals->filter(function ($val) {
                    return in_array($val->status, ['Draft', 'Reject']);
                });
            } elseif ($isAdmin) {
                // Admin เห็นแค่ Reject (เพื่อดูว่าตีกลับอะไรไป)
                $draftApprovals = $allApprovals->filter(function ($val) {
                    return $val->status == 'Reject';
                });
            } else {
                $draftApprovals = collect();
            }

            $salesList = User::where('role', 'sale')->orderBy('name')->pluck('name', 'id');
            $statusList = ['Draft','Waiting', 'Reject', 'Approved', 'Cancel'];

            // ส่งตัวแปรไปที่ View
            return view('approvals.index', compact('mainApprovals', 'draftApprovals', 'salesList', 'statusList'));
        }

    /**
     * 2. กระบวนการสร้าง (SALE)
     */
    public function create()
        {
            return view('approvals.create');
        }

    public function store(Request $request)
        {
            $user = auth()->user();
        
            $data = $request->validate([

                'customer_name'         => 'required|string|max:255',
                'customer_address'      => 'required|string|max:255',
                'customer_subdistrict'  => 'required|string|max:255',
                'customer_district'     => 'required|string|max:255',
                'customer_province'     => 'required|string|max:255',
                'customer_phone'        => 'required|string|max:50',
                'customer_email'        => 'required|string|max:255',
                'car_model'             => 'required|string|max:255',
                'car_color'             => 'nullable|string|max:255',
                'car_options'           => 'nullable|string',
                'car_price'             => 'required|numeric',
                'plus_head'             => 'nullable|numeric',
                'fn'                    => 'nullable|string|max:255',
                'down_percent'          => 'nullable|numeric|between:0,100',
                'down_amount'           => 'nullable|numeric',
                'finance_amount'        => 'nullable|numeric',
                'installment_per_month' => 'nullable|numeric',
                'installment_months'    => 'nullable|integer',
                'interest_rate'         => 'nullable|numeric',
                'amount_ge'             => 'nullable|numeric',
                'amount_reteneion'      => 'nullable|numeric',
                'amount_farmer'             => 'nullable|numeric',
                'amount_welcome'        => 'nullable|numeric',
                'fleet_amount'          => 'nullable|numeric',
                'kickback_amount'       => 'nullable|numeric',
                'campaigns_available'   => 'nullable|string',
                'campaigns_used'        => 'nullable|string',
                'Flight'                => 'nullable|numeric',
                'free_items'            => 'nullable|string',
                'free_items_over'       => 'nullable|string',
                'extra_purchase_items'  => 'nullable|string',
                'decoration_amount'     => 'nullable|numeric',
                'over_campaign_amount'  => 'nullable|numeric',
                'over_decoration_amount' => 'nullable|numeric',
                'over_reason'           => 'nullable|string',
                'remark'                => 'nullable|string',
                'documents1.*'           => 'nullable|mimes:pdf,jpg,jpeg|max:10240',
                'documents2.*'           => 'nullable|mimes:pdf,jpg,jpeg|max:10240',
                'documents3.*'           => 'nullable|mimes:pdf,jpg,jpeg|max:10240',
                
            ]);

            $data['is_commercial_30000'] = $request->has('is_commercial_30000');

            $inputStatus = $request->input('status', 'Waiting');

            $approval = Approval::create(array_merge($data, [
                'group_id'      => 0, 
                'version'       => 1,
                'status'        => $inputStatus,
                'created_by'    => strtoupper($user->role),
                'sales_name'    => $user->name,
                'sales_user_id' => $user->id,
                'remark' => $request->input('remark'),
            ]));

            // อัปเดต group_id
            $approval->update(['group_id' => $approval->id]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('documents', 'public');

                    $approval->documents()->create([
                        'file_path' => $path
                    ]);
                }
            }

            // ใส่รายชื่ออีเมลที่ต้องการส่งให้ครบที่นี่ (คั่นด้วยลูกน้ำ)
            if ($inputStatus === 'Waiting') {
                try {
                    $emails = [
                'snryu.work@gmail.com'    
                ];
                Mail::to($emails)->send(new ApprovalMail($approval, 'new'));
                } catch (\Exception $e) {}
            }

            $approval->sale_type_options = json_encode($request->input('sale_types', []), JSON_UNESCAPED_UNICODE);

                return redirect()->route('approvals.index')->with('success', 
                        ($inputStatus === 'Draft' ? 'บันทึกร่างเรียบร้อย' : 'ส่งใบขออนุมัติเรียบร้อย')
                );
        }
    
    // Admin Action (ฟังก์ชันรวมเพื่อลดความซ้ำซ้อน)
    public function updateStatus(Request $request, $group_id)
        {
            // 1. ตรวจสอบว่ามีสถานะส่งมาไหม
            $request->validate([
                'status' => 'required|in:Approved,Reject',
                'remark' => 'nullable|string'
            ]);

            // 2. อัปเดตทุกใบใน Group เดียวกันให้เป็นสถานะใหม่
            $affected = \App\Models\Approval::where('group_id', $group_id)->update([
                'status' => $request->status,
                'remark' => $request->input('remark'),
                'updated_at' => now()
            ]);

            if ($affected === 0) {
                abort(404, 'ไม่พบข้อมูลใบอนุมัติกลุ่มนี้');
            }

            // 3. สำคัญมาก: ต้องเด้งหน้ากลับไปที่เดิมพร้อมข้อความแจ้งเตือน
            return redirect()->route('approvals.index')
                            ->with('success', 'เปลี่ยนสถานะเป็น ' . $request->status . ' เรียบร้อยแล้ว');
        }

    // กรณีถูก Reject: Sale สร้างเวอร์ชันใหม่เพื่อแก้ไข
    public function createNewVersion($groupId)
        {
            $latest = Approval::where('group_id', $groupId)->orderByDesc('version')->firstOrFail();

            $newVersion = $latest->replicate(); 
            $newVersion->version = $latest->version + 1;
            $newVersion->status = 'Draft';
            $newVersion->save();

            return redirect()->route('approvals.edit', $groupId);
        
        }

    // ส่วนของฟังก์ชัน edit
    public function edit($id)
        {
            $approval = Approval::findOrFail($id);
            $user = auth()->user();

            // แก้เงื่อนไข: ยอมให้ Sale เจ้าของงาน OR Admin เข้ามาแก้ได้
            $isOwner = ($user->role === 'sale' && $approval->sales_user_id === $user->id);
            $isAdmin = ($user->role === 'admin');

            if (!$isOwner && !$isAdmin) {
                abort(403, 'คุณไม่มีสิทธิ์แก้ไขเอกสารนี้');
            }

            return view('approvals.edit', compact('approval'));
        }

    // ส่วนของฟังก์ชัน update (เพื่อให้เก็บประวัติฉบับเก่าไว้)
    public function update(Request $request, $id)
        {
            $oldVersion = Approval::findOrFail($id);

            // 1. ตรวจสอบสิ่งที่เปลี่ยนแปลง (Compare Data)
            $changes = [];
            $fieldsToCheck = [
                'customer_name'         => 'ชื่อลูกค้า',
                'customer_address'      => 'ที่อยู่',
                'customer_subdistrict'  => 'ตำบล',
                'customer_district'     => 'อำเภอ',
                'customer_province'     => 'จังหวัด',
                'customer_phone'        => 'เบอร์โทร',
                'customer_email'        => 'อีเมล',
                'car_model'             => 'รุ่นรถ',
                'car_color'             => 'สี',
                'car_options'           => 'ออฟชั่น',
                'car_price'             => 'ราคารถ',
                'plus_head'             => 'บวกหัว (บาท)',
                'fn'                    => 'F/N',
                'down_percent'          => 'เงินดาวน์ (%)',
                'down_amount'           => 'เงินดาวน์ (บาท)',
                'installment_months'    => 'จำนวนงวด',
                'installment_per_month' => 'งวดละ (บาท)',
                'interest_rate'         => 'ดอกเบี้ย (%)',
                'finance_amount'        => 'ยอดจัด (บาท)',
                'Chassis'               => 'คัชซี',
                'stock_number'          => 'เลขสต๊อก',
                'com_fn_option'         => 'รหัสแคมเปญ',
                'Flight'                => 'หัก (บาท)',
                'insurance_used'        => 'ใช้จริง (บาท)',
                'kickback_amount'       => 'Kickback (บาท)', 
                'com_option'            => 'Com F/N',
                'com_fn_amount'         => 'จำนวน (บาท)',
                'free_items'            => 'รายการของแถม',
                'free_items_over'       => 'รายการของแถมเกิน',
                'extra_purchase_items'  => 'รายการซื้อเพิ่ม',
                'campaigns_available'   => 'แคมเปญที่มี',
                'campaigns_used'        => 'แคมเปญที่ใช้',
                'discount_cash'         => 'ส่วนลด (เงินสดดาวน์) (บาท)',
                'pickup_payment'        => 'รับรถจ่ายดาวน์/สด (บาท)',
                'decoration_cost'       => 'จ่ายของแต่ง',
                'decoration_amount'     => 'รวมทั้งหมด',
                'is_commercial_30000'   => 'Commercial / การแต่ง',
                'decoration_list'       => 'รายการแต่ง',
                'decoration_value'      => 'มูลค่า (บาท)',
                'over_campaign_amount'  => 'เกินแคมเปญ (บาท)',
                'over_campaign_status'  => 'สถานะ',
                'over_decoration_amount'=>'เกินของตกแต่ง (บาท)',
                'over_reason'           =>'สาเหตุขอเกิน',
                'amount_ge'             => 'จำนวนGE (บาท)',
                'amount_reteneion'      => 'จำนวนreteneion (บาท)',
                'amount_farmer'         => 'จำนวนเกษตร (บาท)',
                'amount_welcome'        => 'จำนวนwelcome (บาท)',
                'fleet_amount'          => 'Fleet (บาท)',
                'documents1.*'          => 'ไฟล์1',
                'documents2.*'          => 'ไฟล์2',
                'documents3.*'          => 'ไฟล์3',
                'sc_signature'          => 'SC',
                'sale_com_signature'    => 'Com การขาย (ชื่อ)',
                'remark'                => 'หมายเหตุ',
                // เพิ่มฟิลด์อื่นๆ ที่อยากให้เช็คได้ที่นี่
            ];

                $changes = [];
                
            // วนลูปเช็คค่าเก่า vs ค่าใหม่
            foreach ($fieldsToCheck as $field => $label) {
                $oldValue = $oldVersion->$field ?? null;
                
            if ($field === 'sale_type_options') {
                $newValue = $request->sale_types ?? []; 
            } else {
                $newValue = $request->$field ?? null;
            }

                $val1 = is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : (string)$newValue;
                $val2 = is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : (string)$oldValue;

                if ($val1 !== $val2) {
                    $changes[] = [
                        'field' => $label,
                        'old' => $val2, // แสดงค่าเก่าในรูปแบบ string
                        'new' => $val1  // แสดงค่าใหม่ในรูปแบบ string
                    ];
                }
            }

            // 2. สร้าง Version ใหม่
            $newVersion = $oldVersion->replicate(); 
            $newVersion->fill($request->all());
            
            // *** ต้องบันทึกค่าประเภทการขาย (JSON) ลงใน Model ใหม่ด้วย ***
            if($request->has('sale_types')) {
                $newVersion->sale_type_options = json_encode($request->sale_types, JSON_UNESCAPED_UNICODE);
            } else {
                $newVersion->sale_type_options = json_encode([]);
            }

            $newVersion->group_id = $oldVersion->group_id; 
            $newVersion->version = $oldVersion->version + 1;
            $newVersion->status = 'Waiting'; 
            $newVersion->save();

            // --- ส่ง Email แจ้งเตือน (Update) ---
            // ส่งเฉพาะเมื่อมีการแก้ไขข้อมูลสำคัญ
            if (count($changes) > 0) {
                // ลบ try...catch ออกเช่นกัน:
                $emails = ['Admin@example.com'];
                Mail::to($emails)->send(new ApprovalMail($newVersion, 'update', $changes));
                
            }

            return redirect()->route('approvals.index')->with('success', 'ส่งเวอร์ชันใหม่เพื่อตรวจสอบแล้ว');
        }

    public function destroy($groupId)
        {
            $latest = Approval::where('group_id', $groupId)->orderByDesc('version')->firstOrFail();

            if (Auth::user()->role === 'sale' && $latest->sales_user_id !== Auth::id()) {
                abort(403, 'คุณไม่มีสิทธิ์ลบเอกสารนี้ เนื่องจากไม่ใช่เจ้าของ หรือ แอดมิน');
            }

            if ($latest->status === 'Approved') {
                return back()->with('error', 'เอกสารอนุมัติแล้ว ไม่อนุญาตให้ลบ');
            }

            Approval::where('group_id', $groupId)->delete();
            return redirect()->route('approvals.index')->with('success', 'ลบเอกสารเรียบร้อย');
        }
        // ฟังก์ชันสำหรับ Export Pdf
    public function exportPdf($id)
        { 
            $approval = Approval::findOrFail($id);

            $pdf = Pdf::loadView('approvals.pdf', compact('approval'))
                    ->setPaper('A4', 'portrait');

            return $pdf->stream('approval_' . $approval->id . '.pdf');
        }

        // ฟังก์ชันสำหรับ Export Excel
    public function exportExcel() 
        {
            // ตั้งชื่อไฟล์เป็น All_Approvals.xlsx
            return Excel::download(new ApprovalExport, 'All_Approvals.xlsx');
        }

        // ฟังก์ชันสำหรับ Export CSV
    public function exportCsv()
        {
            return Excel::download(new ApprovalExport, 'GoogleSheets_Approvals.csv', \Maatwebsite\Excel\Excel::CSV);
        }

    public function show($id)
        {
            // ดึงข้อมูลฉบับปัจจุบัน
            $current = Approval::findOrFail($id);

            // ดึงประวัติทั้งหมดในกลุ่มเดียวกัน เรียงตาม Version ล่าสุด
            $history = Approval::where('group_id', $current->group_id)
                ->orderBy('version', 'desc')
                ->get();

            return view('approvals.show', compact('current', 'history'));
        }

        // 4. การแสดงผลและจัดการข้อมูล
    // public function showGroup($groupId)
    //    {
    //        $approvals = Approval::where('group_id', $groupId)->orderBy('version', 'asc')->get();
    //        $current = $approvals->last(); // แสดงข้อมูลเวอร์ชันล่าสุดเป็นหลัก
    //        return view('approvals.show', compact('approvals', 'current'));
    //    }
    
    public function getVersionDetail($id)
        {
            $approval = Approval::findOrFail($id);
            
            // คืนค่าเป็น View เล็กๆ (Partial) ที่มีเฉพาะตารางข้อมูล
            return view('approvals.partials.version_detail', compact('approval'))->render();
        }
        
   public function fetchVersion($id)
        {
            $approval = \App\Models\Approval::findOrFail($id);
            return view('approvals.partials.preview', compact('approval'))->render();
        }

    public function previewVersion($approvalId, $version)
    {
        $approval = Approval::where('id', $approvalId)
            ->where('version', $version)
            ->firstOrFail();

        return view('approvals.partials.preview', compact('approval'));
    }
}
