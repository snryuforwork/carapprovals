@extends('layout') 

@section('content')

    <div class="d-flex justify-content-between mb-3">
        @php
            $currentOptions = is_array($approval->sale_type_options) 
                ? $approval->sale_type_options 
                : json_decode($approval->sale_type_options ?? '[]', true);
        @endphp

        <button type="button" onclick="history.back()" class="btn btn-secondary">
            ← ย้อนกลับ
        </button>
    </div>
    <ins><h2>แก้ไขใบอนุมัติ</h2></ins><br>

{{-- ส่วนหัวฟอร์ม --}}
<form action="{{ route('approvals.update', $approval->id) }}" method="POST">
    @csrf
    @method('PUT')
    
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

    <div class="row">
    <div class="col-6 mb-3">
        <label class="form-label">วันที่ขอแคมเปญ</label>
        <input type="date" class="form-control" name="request_date"
            value="{{ old('request_date', $approval->request_date) }}">
    </div>
    <div class="col-6 mb-3">
        <label class="form-label">วันที่จะส่งมอบรถ</label>
        <input type="date" class="form-control" name="delivery_date"
            value="{{ old('delivery_date', $approval->delivery_date) }}">
    </div>
    </div>

 {{-- 1. ข้อมูลลูกค้า --}}
    <div class="section-title">ข้อมูลลูกค้า</div>

    <div class="mb-3">
        <label class="form-label">ชื่อลูกค้า<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="customer_name" required
            value="{{ old('customer_name', $approval->customer_name) }}">
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ที่อยู่<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_address" required
            value="{{ old('customer_address', $approval->customer_address) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ตำบล<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_subdistrict" required
            value="{{ old('customer_subdistrict', $approval->customer_subdistrict) }}">
        </div>
     </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">อำเภอ<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_district" required
                value="{{ old('customer_district', $approval->customer_district) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จังหวัด<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_province" required
                value="{{ old('customer_province', $approval->customer_province) }}">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">เบอร์โทร<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="customer_phone" required
            value="{{ old('customer_phone', $approval->customer_phone) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">อีเมล<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="customer_email" required
        value="{{ old('customer_email', $approval->customer_email) }}">
    </div>

    {{-- 2. ข้อมูลรถ --}}
    <div class="section-title">ข้อมูลรถ</div>
        <div class="mb-3">
            <label class="form-label">รุ่นรถ<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="car_model" required
            value="{{ old('car_model', $approval->car_model) }}">
        </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">สี<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="car_color" required
                value="{{ old('car_color', $approval->car_color) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ออฟชั่น</label>
            <input type="text" class="form-control" name="car_options"
                value="{{ old('car_options', $approval->car_options) }}">
        </div>
    </div>
     <div class="mb-3">
        <label class="form-label">ราคา (บาท)</label>
        <input id="car_price" class="form-control" type="number" step="0.01" name="car_price"
            value="{{ old('car_price', $approval->car_price) }}">
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">บวกหัว (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="plus_head"
                value="{{ old('plus_head', $approval->plus_head) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">F/N</label>
            <input type="text" class="form-control" name="fn"
                value="{{ old('fn', $approval->fn) }}">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ดาวน์ (%)</label>
            <input id="down_percent" class="form-control" type="number" step="0.01" name="down_percent"
                value="{{ old('down_percent', $approval->down_percent) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ดาวน์ (บาท)</label>
            <input id="down_amount" class="form-control" type="number" step="0.01" name="down_amount" placeholder="--- คำนวนอัตโนมัติ ---"
                value="{{ old('down_amount', $approval->down_amount) }}">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">จำนวนงวด</label>
            <input id="installment_months" class="form-control" type="number" name="installment_months"
                value="{{ old('installment_months', $approval->installment_months) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">งวดละ (บาท)</label>
            <input id="installment_per_month" class="form-control" type="number" readonly name="installment_per_month" placeholder="--- คำนวนอัตโนมัติ ---"
                value="{{ old('installment_per_month', $approval->installment_per_month) }}">
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ดอกเบี้ย (%)</label>
            <input id="interest_rate" class="form-control" type="number" step="0.01" name="interest_rate"
                value="{{ old('interest_rate', $approval->interest_rate) }}">
        </div>
    </div>
        
        <div class="mb-3">
            <label class="form-label">เลขสต๊อก</label>
            <input type="text" step="0.01" class="form-control" name="stock_number"
                value="{{ old('stock_number', $approval->stock_number) }}">
        </div>

    <div class="section-title"></div></br>

    <div class="row">
        <div class="col-6 mb-3">
                <label class="form-label">รหัสแคมเปญ</label>
                <select class="form-select" name="com_option">
                    <option value="">-- เลือก --</option>
                    <option value="N" {{ old('com_option', $approval->com_option) == 'N' ? 'selected' : '' }}>N</option>
                    <option value="L" {{ old('com_option', $approval->com_option) == 'L' ? 'selected' : '' }}>L</option>
                    <option value="LDP" {{ old('com_option', $approval->com_option) == 'LDP' ? 'selected' : '' }}>LDP</option>
                    <option value="90D" {{ old('com_option', $approval->com_option) == '90D' ? 'selected' : '' }}>90D</option>
                    <option value="SCP" {{ old('com_option', $approval->com_option) == 'SCP' ? 'selected' : '' }}>SCP</option>
                    <option value="FCP" {{ old('com_option', $approval->com_option) == 'FCP' ? 'selected' : '' }}>FCP</option>
                </select>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">หัก (บาท)</label>
            <input type="number" class="form-control" name="Flight"
            value="{{ old('Flight', $approval->Flight) }}">
        </div>
    </div>
    

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">ประเภทการขาย</label><br>
            <input type="checkbox" name="sale_types[]" value="GE"
                {{ in_array('GE', old('sale_types', $currentOptions)) ? 'checked' : '' }}> GE
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จำนวน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="amount_ge" 
                value="{{ old('amount_ge', $approval->amount_ge) }}">
        </div>

        <div class="col-6 mb-3">
            <input type="checkbox" name="sale_types[]" value="RETENTION"
                {{ in_array('RETENTION', old('sale_types', $currentOptions)) ? 'checked' : '' }}> RETENTION
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จำนวน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="amount_retention"
                value="{{ old('amount_retention', $approval->amount_retention) }}">
        </div>

        <div class="col-6 mb-3">
            <input type="checkbox" name="sale_types[]" value="FARMER"
                {{ in_array('FARMER', old('sale_types', $currentOptions)) ? 'checked' : '' }}> เกษตรกร
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จำนวน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="amount_farmer"
                value="{{ old('amount_farmer', $approval->amount_farmer) }}">
        </div>

        <div class="col-6 mb-3">
            <input type="checkbox" name="sale_types[]" value="Welcome"
                {{ in_array('Welcome', old('sale_types', $currentOptions)) ? 'checked' : '' }}> Welcome
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">จำนวน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="amount_welcome"
                value="{{ old('amount_welcome', $approval->amount_welcome) }}">
        </div>
    </div>
    
    <div class="mb-3">
            <input type="checkbox" name="options5[]" value="Fleet">
            <label class="form-label">Fleet (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="fleet_amount"
                value="{{ old('fleet_amount', $approval->fleet_amount) }}">
        </div>

    <div class="row">    
        <div class="col-6 mb-3">
            <label class="form-label">หักประกัน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="insurance_deduct"
                value="{{ old('insurance_deduct', $approval->insurance_deduct) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">ใช้จริง (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="insurance_used"
                value="{{ old('insurance_used', $approval->insurance_used) }}">
        </div>
    </div>  

    <div class="mb-3">
        <label class="form-label">Kickback (บาท)</label>
        <input type="number" step="0.01" class="form-control" name="kickback_amount"
            value="{{ old('kickback_amount', $approval->kickback_amount) }}">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Com F/N</label>
        <select class="form-select" name="com_fn_option">
            <option value="">-- เลือก --</option>
            <option value="4" {{ old('com_fn_option', $approval->com_fn_option) == '4' ? 'selected' : '' }}>4</option>
            <option value="8" {{ old('com_fn_option', $approval->com_fn_option) == '8' ? 'selected' : '' }}>8</option>
            <option value="10" {{ old('com_fn_option', $approval->com_fn_option) == '10' ? 'selected' : '' }}>10</option>
            <option value="12" {{ old('com_fn_option', $approval->com_fn_option) == '12' ? 'selected' : '' }}>12</option>
            <option value="14" {{ old('com_fn_option', $approval->com_fn_option) == '14' ? 'selected' : '' }}>14</option>
            <option value="16" {{ old('com_fn_option', $approval->com_fn_option) == '16' ? 'selected' : '' }}>16</option>
        </select>
    </div>
    <div class="col-6 mb-3">
            <label class="form-label">จำนวน (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="com_fn_amount"
                value="{{ old('com_fn_amount', $approval->com_fn_amount) }}">
        </div>
   
    <div class="mb-3">
        <label class="form-label">รายการของแถม</label>
        <textarea rows="2" class="form-control" name="free_items">{{ old('free_items', $approval->free_items) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">รายการของแถมเกิน</label>
        <textarea rows="2" class="form-control" name="free_items_over">{{ old('free_items_over', $approval->free_items_over) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">รายการซื้อเพิ่ม</label>
        <textarea rows="2" class="form-control" name="extra_purchase_items">{{ old('extra_purchase_items', $approval->extra_purchase_items) }}</textarea>
    </div>


    {{-- 19–20 แคมเปญ --}}
    <div class="section-title">แคมเปญ</div><br>

    <div class="mb-3">
        <label class="form-label">แคมเปญที่มี</label>
        <textarea rows="2" class="form-control" name="campaigns_available">{{ old('campaigns_available', $approval->campaigns_available) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">แคมเปญที่ใช้</label>
        <textarea rows="2" class="form-control" name="campaigns_used">{{ old('campaigns_used', $approval->campaigns_used) }}</textarea>
    </div>

    {{-- 4. แก้ไข: decoration_amount ซ้ำกัน 5 จุด!!! ต้องเปลี่ยนชื่อให้ตรง DB --}}
    <div class="col-6 mb-3">
        <label class="form-label">ส่วนลด (เงินสดดาวน์)</label>
        <input type="number" step="0.01" class="form-control" name="discount_cash" 
        value="{{ old('discount_cash', $approval->discount_cash ?? '') }}"> {{-- ต้องแก้ name ให้ตรง DB --}}
    </div>
    <div class="col-6 mb-3">
        <label class="form-label">รับรถจ่ายดาวน์/สด</label>
        <input type="text" step="0.01" class="form-control" name="pickup_payment" 
        value="{{ old('pickup_payment', $approval->pickup_payment ?? '') }}"> {{-- ต้องแก้ name ให้ตรง DB --}}
    </div>
     <div class="col-6 mb-3">
        <label class="form-label">จ่ายของแต่ง</label>
        <input type="number" step="0.01" class="form-control" name="decoration_cost" 
        value="{{ old('decoration_cost', $approval->decoration_cost ?? '') }}"> {{-- ต้องแก้ name ให้ตรง DB --}}
    </div>
     <div class="col-6 mb-3">
        <label class="form-label">รวมทั้งหมด</label>
        <input type="number" step="0.01" class="form-control" name="decoration_amount" id="decoration_amount_input"
            value="{{ old('decoration_amount', $approval->decoration_amount) }}">
    </div>

    {{-- 21–22 commercial / การแต่ง --}}
    <div class="section-title">Commercial / การแต่ง</div><br>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_commercial_30000" value="1" id="comm"
            {{ old('is_commercial_30000', $approval->is_commercial_30000) == 1 ? 'checked' : ''}}>
        <label for="comm" class="form-check-label">commercial 30,000 บาท</label>
    </div>

    <div class="mb-3">
        <label class="form-label">รายการแต่ง</label>
        {{-- 5. แก้ไข: เปลี่ยน name ไม่ให้ซ้ำ --}}
        <textarea rows="2" class="form-control" name="decoration_list">{{ old('decoration_list', $approval->decoration_list ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">มูลค่า (บาท)</label>
        {{-- 6. แก้ไข: เปลี่ยน name ไม่ให้ซ้ำ --}}
        <textarea rows="2" class="form-control" name="decoration_value">{{ old('decoration_value', $approval->decoration_value ?? '') }}</textarea>
    </div>


    {{-- 23–24 เกินแคมเปญ / เกินของแต่ง --}}
    <div class="section-title"></div><br>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">เกินแคมเปญ (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="over_campaign_amount"
            value="{{ old('over_campaign_amount', $approval->over_campaign_amount) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">สถานะ</label>
            <select class="form-select" name="over_campaign_status">
                <option value="">-- เลือก --</option>
                <option value="ไม่เกิน" {{ old('over_campaign_status', $approval->over_campaign_status) == 'ไม่เกิน' ? 'selected' : '' }}>ไม่เกิน</option>
                <option value="เกิน" {{ old('over_campaign_status', $approval->over_campaign_status) == 'เกิน' ? 'selected' : '' }}>เกิน</option>
            </select>
        </div>
    </div>

     <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">เกินของตกแต่ง (บาท)</label>
            <input type="number" step="0.01" class="form-control" name="over_decoration_amount"
                value="{{ old('over_decoration_amount', $approval->over_decoration_amount) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">สถานะ</label>
            <select class="form-select" name="over_decoration_status">
                <option value="">-- เลือก --</option>
                <option value="ไม่เกิน" {{ old('over_decoration_status', $approval->over_decoration_status) == 'ไม่เกิน' ? 'selected' : '' }}>ไม่เกิน</option>
                <option value="เกิน" {{ old('over_decoration_status', $approval->over_decoration_status) == 'เกิน' ? 'selected' : '' }}>เกิน</option>
            </select>
        </div>
    </div>

        <div class="mb-3">
            <label class="form-label">สาเหตุขอเกิน</label>
            <textarea rows="2" class="form-control" name="over_reason">{{ old('over_reason', $approval->over_reason) }}</textarea>
    </div>

    {{-- แนบไฟล์ 1 --}}
    <div class="mb-3">
        <label class="form-label">แนบเอกสาร (PDF / JPG) ไม่เกิน 10MB ต่อไฟล์</label>
        @if(!empty($approval->documents1))
            <div class="mb-2 p-2 bg-light border rounded">
                <span class="text-success"><i class="fas fa-check-circle"></i> มีไฟล์แนบเดิมแล้ว:</span>
                {{-- สมมติว่าเก็บชื่อไฟล์เป็น JSON หรือ Text ธรรมดา --}}
                <a href="{{ asset('storage/' . $approval->documents1) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                    ดูไฟล์ปัจจุบัน
                </a>
            </div>
        @endif

        {{-- ช่องสำหรับอัปโหลดใหม่ (ไม่ต้องใส่ value) --}}
        <input type="file" name="documents1[]" class="form-control" accept=".pdf,.jpg,.jpeg" multiple>
        <small class="text-muted">หากไม่อัปโหลดใหม่ ระบบจะใช้ไฟล์เดิม</small>
    </div>

    {{-- แนบไฟล์ 2 --}}
    <div class="mb-3">
        <label class="form-label">แนบเอกสาร (PDF / JPG) ไม่เกิน 10MB ต่อไฟล์</label>
        @if(!empty($approval->documents2))
            <div class="mb-2 p-2 bg-light border rounded">
                <span class="text-success"><i class="fas fa-check-circle"></i> มีไฟล์แนบเดิมแล้ว:</span>
                {{-- สมมติว่าเก็บชื่อไฟล์เป็น JSON หรือ Text ธรรมดา --}}
                <a href="{{ asset('storage/' . $approval->documents2) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                    ดูไฟล์ปัจจุบัน
                </a>
            </div>
        @endif

        {{-- ช่องสำหรับอัปโหลดใหม่ (ไม่ต้องใส่ value) --}}
        <input type="file" name="documents2[]" class="form-control" accept=".pdf,.jpg,.jpeg" multiple>
        <small class="text-muted">หากไม่อัปโหลดใหม่ ระบบจะใช้ไฟล์เดิม</small>
    </div>

    {{-- แนบไฟล์ 3 --}}
    <div class="mb-3">
        <label class="form-label">แนบเอกสาร (PDF / JPG) ไม่เกิน 10MB ต่อไฟล์</label>
        @if(!empty($approval->documents3))
            <div class="mb-2 p-2 bg-light border rounded">
                <span class="text-success"><i class="fas fa-check-circle"></i> มีไฟล์แนบเดิมแล้ว:</span>
                {{-- สมมติว่าเก็บชื่อไฟล์เป็น JSON หรือ Text ธรรมดา --}}
                <a href="{{ asset('storage/' . $approval->documents3) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                    ดูไฟล์ปัจจุบัน
                </a>
            </div>
        @endif

        {{-- ช่องสำหรับอัปโหลดใหม่ (ไม่ต้องใส่ value) --}}
        <input type="file" name="documents3[]" class="form-control" accept=".pdf,.jpg,.jpeg" multiple>
        <small class="text-muted">หากไม่อัปโหลดใหม่ ระบบจะใช้ไฟล์เดิม</small>
    </div>

    {{-- 25–27 --}}
    <div class="section-title"></div><br>
    <div class="row">    
        <div class="col-6 mb-3">
            <label class="form-label">SC (ชื่อ)</label>
            <input type="text" class="form-control" name="sc_signature"
            value="{{ old('sc_signature', $approval->sc_signature) }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Com การขาย (ชื่อ)</label>
            <input type="text" class="form-control" name="sale_com_signature"
            value="{{ old('sale_com_signature', $approval->sale_com_signature) }}">
        </div>
    </div> 

    <div class="mb-3">
        <label for="remark" class="form-label fw-bold">หมายเหตุ (ถ้ามี):</label>
        <textarea name="remark" class="form-control" rows="2" 
        placeholder="ระบุข้อความเพิ่มเติม...">{{ old('remark', $approval->remark ?? '') }}</textarea>
    </div>

    <button class="btn btn-primary w-100 mt-3">บันทึกและส่ง</button>

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
</form>
@endsection