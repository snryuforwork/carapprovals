
@extends('layout')

@section('title', 'สร้างใบอนุมัติ (Mobile UI)')

@section('content')

<style>
    .section-title {
        font-weight: bold;
        font-size: 18px;
        margin-top: 15px;
        padding: 10px 0;
        border-bottom: 2px solid #c10000ff;
    }
    .sub {
        font-size: 14px;
        color: #555555ff;
        margin-bottom: 5px;
    }
</style>

<form method="POST" action="{{ route('approvals.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>พบข้อผิดพลาด:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <button type="button" onclick="history.back()" class="btn btn-secondary">
            ← ย้อนกลับ
        </button>
    </div>
    
    <ins><h2>ใบขออนุมัติการขายรถยนต์</h2></ins>
    <br>

    {{-- DATE --}}
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">วันที่ขอแคมเปญ <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="request_date" required>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">วันที่จะส่งมอบรถ</label>
            <input type="date" class="form-control" name="delivery_date">
        </div>
    </div>


    {{-- 1. ข้อมูลลูกค้า --}}
    <div class="section-title">ข้อมูลลูกค้า</div>

    <div class="mb-3">
        <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="customer_name" required>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_address" required>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ตำบล <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_subdistrict" required>
        </div>
     </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">อำเภอ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_district" required>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จังหวัด <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_province" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">เบอร์โทร <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="customer_phone" required>
    </div>

    <div class="mb-3">
        <label class="form-label">อีเมล <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="customer_email" required>
    </div>

    {{-- 2. ข้อมูลรถ --}}
    <div class="section-title">ข้อมูลรถ</div>

        <div class="mb-3">
            <label class="form-label">รุ่นรถ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="car_model" required>
        </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">สี <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="car_color" required>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ออฟชั่น</label>
            <input type="text" class="form-control" name="car_options">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">ราคา (บาท)</label>
        <input id="car_price" class="form-control" type="number" step="0.01" name="car_price">
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">บวกหัว (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="plus_head">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">F/N</label>
            <input type="text" class="form-control" name="fn">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ดาวน์ (%)</label>
            <input id="down_percent" class="form-control" type="number" step="0.01" name="down_percent">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ดาวน์ (บาท)</label>
            <input id="down_amount" class="form-control" type="number" step="0.01" name="down_amount" placeholder="--- คำนวนอัตโนมัติ ---">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">จำนวนงวด</label>
            <input id="installment_months" class="form-control" type="number" name="installment_months">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">งวดละ (บาท)</label>
             <input id="installment_per_month" class="form-control" type="number" readonly name="installment_per_month" placeholder="--- คำนวนอัตโนมัติ ---">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ดอกเบี้ย (%)</label>
            <input id="interest_rate" class="form-control" type="number" step="0.01" name="interest_rate">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ยอดจัด (บาท)</label>
            <input id="finance_amount" class="form-control" type="number" readonly name="finance_amount">
        </div>
    </div>

        <div class="section-title"></div></br>

        <div class="mb-3">
            <label class="form-label">คัชซี</label>
            <input type="number" step="0.01" class="form-control" name="Chassis">
        </div>
        <div class="mb-3">
            <label class="form-label">เลขสต๊อก</label>
            <input type="text" step="0.01" class="form-control" name="stock_number">
        </div>

    <div class="section-title"></div></br>

    <div class="row">
        <div class="col-6 mb-3">
                <label class="form-label">รหัสแคมเปญ</label>
                <select class="form-select" name="com_fn_option">
                    <option value="">-- เลือก --</option>
                    <option value="์N">N</option>
                    <option value="L">L</option>
                    <option value="LDP">LDP</option>
                    <option value="90D">90D</option>
                    <option value="SCP">SCP</option>
                    <option value="FCP">FCP</option>
                </select>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">หัก (บาท)</label>
            <input type="number" class="form-control" name="Flight">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ประเภทการขาย</label><br>
            <input type="checkbox" name="sale_types[]" value="GE"> GE
        </div>
        <div class="col-6 mb-3">
            <input type="number" step="0.01" class="form-control" name="amount_ge" placeholder="จำนวน (บาท)">
        </div>

        <div class="col-6 mb-3">
            <input type="checkbox" name="sale_types[]" value="RETENTION"> Retention
        </div>
        <div class="col-6 mb-3">
            <input type="number" step="0.01" class="form-control" name="amount_retention" placeholder="จำนวน (บาท)">
        </div>

        <div class="col-6 mb-3">
            <input type="checkbox" name="sale_types[]" value="FARMER"> เกษตรกร
        </div>
        <div class="col-6 mb-3">
            <input type="number" step="0.01" class="form-control" name="amount_farmer" placeholder="จำนวน (บาท)">
        </div>

        <div class="col-6 mb-3">
            <input type="checkbox" name="sale_types[]" value="Welcome"> Welcome
        </div>
        <div class="col-6 mb-3">
            <input type="number" step="0.01" class="form-control" name="amount_welcome" placeholder="จำนวน (บาท)">
        </div>
    </div>

        <div class="mb-3">
            <input type="checkbox" name="options5[]">
            <label class="form-label">Fleet (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="fleet_amount">
        </div>

    <div class="row">    
        <div class="col-6 mb-3">
            <label class="form-label">หักประกัน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="insurance_deduct">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ใช้จริง (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="insurance_used">
        </div>
    </div>    

        <div class="mb-3">
            <label class="form-label">Kickback (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="kickback_amount">
        </div>
    
        <div class="mb-3">
            <label class="form-label">Com F/N</label>
            <select class="form-select" name="com_option">
                <option value="">-- เลือก --</option>
                <option value="4">4</option>
                <option value="8">8</option>
                <option value="10">10</option>
                <option value="12">12</option>
                <option value="14">14</option>
                <option value="16">16</option>
            </select>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จำนวน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="com_fn_amount">
        </div>


    {{-- 13–17 ของแถม --}}
    <div class="section-title">รายการของแถม</div>

    <div class="mb-3">
        <label class="form-label">รายการของแถม</label>
        <textarea rows="2" class="form-control" name="free_items"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">รายการของแถมเกิน</label>
        <textarea rows="2" class="form-control" name="free_items_over"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">รายการซื้อเพิ่ม</label>
        <textarea rows="2" class="form-control" name="extra_purchase_items"></textarea>
    </div>


    {{-- 19–20 แคมเปญ --}}
    <div class="section-title">แคมเปญ</div><br>

    <div class="mb-3">
        <label class="form-label">แคมเปญที่มี</label>
        <textarea rows="2" class="form-control" name="campaigns_available"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">แคมเปญที่ใช้</label>
        <textarea rows="2" class="form-control" name="campaigns_used"></textarea>
    </div>
     <div class="col-6 mb-3">
        <label class="form-label">ส่วนลด (เงินสดดาวน์) (บาท)</label>
        <input type="number" step="0.01" class="form-control" name="discount_cash">
    </div>
    <div class="col-6 mb-3">
        <label class="form-label">รับรถจ่ายดาวน์/สด (บาท)</label>
        <input type="text" step="0.01" class="form-control" name="pickup_payment">
    </div>
     <div class="col-6 mb-3">
        <label class="form-label">จ่ายของแต่ง</label>
        <input type="number" step="0.01" class="form-control" name="decoration_cost">
    </div>
    <div class="col-6 mb-3">
        <label class="form-label">รวมทั้งหมด</label>
        <div class="input-group">
            <input type="number" step="0.01" class="form-control" name="decoration_amount" id="calc_input">
            <button class="btn btn-outline-secondary" type="button" onclick="openCalculator()">
                <i class="bi bi-calculator"></i> 🖩
            </button>
        </div>
    </div>
    <div class="modal fade" id="calcModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div id="calculator-display" class="h3 border p-2 mb-3 bg-light text-end">0</div>
                    <div class="row g-2">
                        @foreach(['7','8','9','/','4','5','6','*','1','2','3','-','0','.','C','+'] as $btn)
                            <div class="col-3">
                                <button class="btn btn-secondary w-100 py-3" onclick="pressKey('{{ $btn }}')">{{ $btn }}</button>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-2" onclick="applyResult()">ยืนยันค่านี้</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 21–22 commercial / การแต่ง --}}
    <div class="section-title">Commercial / การแต่ง</div><br>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_commercial_30000" value="1" id="comm">
        <label for="comm" class="form-check-label">commercial 30,000 บาท</label>
    </div>

    <div class="mb-3">
        <label class="form-label">รายการแต่ง</label>
        <textarea rows="2" class="form-control" name="decoration_list"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">มูลค่า (บาท)</label>
        <textarea rows="2" class="form-control" name="decoration_value"></textarea>
    </div>


    {{-- 23–24 เกินแคมเปญ / เกินของแต่ง --}}
    <div class="section-title"></div><br>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">เกินแคมเปญ (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="over_campaign_amount">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">สถานะ</label>
            <select class="form-select" name="over_campaign_status">
                <option value="">-- เลือก --</option>
                <option value="ไม่เกิน">ไม่เกิน</option>
                <option value="เกิน">เกิน</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">เกินของตกแต่ง (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="over_decoration_amount">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">สถานะ</label>
            <select class="form-select" name="over_decoration_status">
                <option value="">-- เลือก --</option>
                <option value="ไม่เกิน">ไม่เกิน</option>
                <option value="เกิน">เกิน</option>
            </select>
        </div>
    </div>

        <div class="mb-3">
            <label class="form-label">สาเหตุขอเกิน</label>
            <textarea rows="2" class="form-control" name="over_reason"></textarea>
        </div>

         {{-- แนบไฟล์ 1 --}}
    <div class="mb-3">
        <label class="form-label">แนบเอกสาร (PDF / JPG) ไม่เกิน 10MB ต่อไฟล์</label>
        <input type="file" 
            name="documents1[]" 
            class="form-control" 
            accept=".pdf,.jpg,.jpeg"
            multiple>
    </div>
    {{-- แนบไฟล์ 2 --}}
    <div class="mb-3">
        <label class="form-label">แนบเอกสาร (PDF / JPG) ไม่เกิน 10MB ต่อไฟล์</label>
        <input type="file" 
            name="documents2[]" 
            class="form-control" 
            accept=".pdf,.jpg,.jpeg"
            multiple>
    </div>
    {{-- แนบไฟล์ 3 --}}
    <div class="mb-3">
        <label class="form-label">แนบเอกสาร (PDF / JPG) ไม่เกิน 10MB ต่อไฟล์</label>
        <input type="file" 
            name="documents3[]" 
            class="form-control" 
            accept=".pdf,.jpg,.jpeg"
            multiple>
    </div>

    {{-- ลงชื่อ --}}
    <div class="section-title"></div><br>
    <div class="row">    
        <div class="col-6 mb-3">
            <label class="form-label">SC (ชื่อ)</label>
            <input type="text" step="0.01" class="form-control" name="sc_signature">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Com การขาย (ชื่อ)</label>
            <input type="text" step="0.01" class="form-control" name="sale_com_signature">
        </div>
    </div>  

    <div class="mb-3">
        <label for="remark" class="form-label fw-bold">หมายเหตุ (ถ้ามี):</label>
        <textarea name="remark" class="form-control" rows="2" placeholder="ระบุข้อความเพิ่มเติม...">{{ old('remark', $approval->remark ?? '') }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" name="status" value="Draft" class="btn btn-secondary">
            <i class="fas fa-save"></i> บันทึกร่าง
        </button>

        <button type="submit" name="status" value="Waiting" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> ส่งขออนุมัติ
        </button>
    </div>
    
{{-- ================== SCRIPT เครื่องคิดเลข ================== --}}
<script>
let currentExpression = "";

function openCalculator() {
    var myModal = new bootstrap.Modal(document.getElementById('calcModal'));
    myModal.show();
}

function pressKey(key) {
    const display = document.getElementById('calculator-display');
    if (key === 'C') {
        currentExpression = "";
    } else {
        currentExpression += key;
    }
    display.innerText = currentExpression || "0";
}

function applyResult() {
    try {
        const result = eval(currentExpression); // คำนวณค่า
        document.getElementById('calc_input').value = result.toFixed(2);
        bootstrap.Modal.getInstance(document.getElementById('calcModal')).hide();
    } catch (e) {
        alert("รูปแบบการคำนวณไม่ถูกต้อง");
    }
}
</script>

{{-- ================== SCRIPT คำนวณ ================== --}}
<script>
function calculateFinance() {
    const price = parseFloat(car_price.value) || 0;
    const downPercent = parseFloat(down_percent.value) || 0;
    let downAmount = parseFloat(down_amount.value) || 0;
    const months = parseInt(installment_months.value) || 0;
    const interest = parseFloat(interest_rate.value) || 0;

    if (downPercent > 0) {
        downAmount = price * (downPercent / 100);
        down_amount.value = downAmount.toFixed(2);
    }

    const finance = price - downAmount;
    finance_amount.value = finance.toFixed(2);

    const interestTotal = finance * (interest / 100) * (months / 12);
    const total = finance + interestTotal;

    installment_per_month.value = months > 0
        ? (total / months).toFixed(2)
        : '';
}

document.querySelectorAll(
    '#car_price,#down_percent,#down_amount,#installment_months,#interest_rate'
).forEach(el => el.addEventListener('input', calculateFinance));
</script>
@endsection