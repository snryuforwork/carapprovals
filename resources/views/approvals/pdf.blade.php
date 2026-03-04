<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>ใบขออนุมัติเงื่อนไขการขาย #{{ $approval->id }}</title>
    <style>
        /* กำหนดฟอนต์ภาษาไทย (ต้องมีไฟล์ฟอนต์ใน storage/fonts) */
        @font-face {
            font-family: 'THSarabunNew';
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }

        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 16px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        @page {
            size: A4;
            margin: 1cm; /* เว้นระยะขอบกระดาษ 1 ซม. รอบด้าน */
        }

        .container {
            width: 100%; /* ขนาด A4 */
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
        }

        .header-title {
            font-weight: bold;
            font-size: 18px;
            text-decoration: underline;
            margin-bottom: 10px;
        }
        .info-box {
        border: 1px solid #000;
        padding: 10px;
        width: 45%; /* ปรับความกว้างให้เล็กลงหน่อยเพื่อไม่ให้เบียดขอบ */
        float: right; /* ใช้ float หรือ flex ให้พอดี */
    }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        .bordered td, .bordered th {
            border: 1px solid black;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }

        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 100px;
            padding-left: 5px;
        }

        /* ส่วนจัดหน้าตาราง 3 คอลัมน์หลัก */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr;
            border: 1px solid black;
        }

        .column-box {
            border-right: 1px solid black;
        }

        .column-box:last-child {
            border-right: none;
        }

        .col-header {
            background-color: #f2f2f2;
            border-bottom: 1px solid black;
            font-weight: bold;
            text-align: center;
            padding: 5px;
        }

        .row-item {
            border-bottom: 0.5px solid #eee;
            min-height: 25px;
            padding: 2px 5px;
        }
    </style>
</head>

<body>
<div class="container">
    @php
        $selectedTypes = is_array($approval->sale_type_options) 
            ? $approval->sale_type_options 
            : json_decode($approval->sale_type_options ?? '[]', true);
    @endphp

    <div style="display: flex; justify-content: space-between;">
        <div class="header-title">ใบขออนุมัติเงื่อนไขการขาย YPB </div>
    <table>
        <tr>
            <td>
                วันที่ขอแคมเปญ :<span class="dotted-line">{{ $approval->request_date ?? '-'}}</span> 
                วันที่จะส่งมอบรถ :<span class="dotted-line"> {{ $approval->delivery_date ?? '-'}}</span><br>
            </td>
        </tr>
    </table>
    </div>

    <table>
        <tr>
            <td width="50%">
                รุ่นรถ <span class="dotted-line">{{ $approval->car_model ?? '-' }}</span>
                ออฟชั่น <span class="dotted-line">{{ $approval->car_options ?? '-' }}</span><br>
                สี <span class="dotted-line">{{ $approval->car_color ?? '-' }}</span>
                ราคา <span class="dotted-line">{{ $approval->car_price ?? '-' }}</span><br> 
                บวกหัว <span class="dotted-line">{{ $approval->plus_head ?? '-' }}</span>
                F/N <span class="dotted-line">{{ $approval->fn ?? '-' }}</span><br> 
                ดาวน์ <span class="dotted-line">{{ $approval->down_percent ?? '-' }}</span>% 
                ดาวน์ <span class="dotted-line">{{ $approval->down_amount ?? '-' }}</span>บาท<br>
                ยอดจัด <span class="dotted-line">{{ $approval->finance_amount ?? '-' }}</span>บาท 
                งวดละ <span class="dotted-line">{{ $approval->installment_per_month ?? '-' }}</span> บาท<br>
                จำนวน <span class="dotted-line">{{ $approval->installment_months ?? '-' }}</span> งวด 
                ดอกเบี้ย <span class="dotted-line">{{ $approval->interest_rate ?? '-' }}</span> %<br>
                คัชซี <span class="dotted-line">{{ $approval->Chassis ?? '-' }}</span> %<br>
                เลขสต๊อก <span class="dotted-line">{{ $approval->stock_number ?? '-' }}</span> %<br>

            </td>
            <td width="50%">
                รหัสแคมเปญ <span class="dotted-line">{{ $approval->com_fn_option ?? '-' }}</span>
                หัก <span class="bold">{{ $approval->Flight ?? '-' }}</span> บาท<br>
    <div class="sale-type-section" style="border: 1px solid black; padding: 10px;">
        ประเภทการขาย 
            <span>
                [{{ in_array('GE', $selectedTypes) ? '✓' : ' ' }}] GE</span>
                จำนวน <b>{{ number_format($approval->amount_ge ?? 0, 2) }}</b> บาท<br>

            <span>[{{ in_array('RETENTION', $selectedTypes) ? '✓' : ' ' }}] Retention</span> 
                จำนวน <b>{{ number_format($approval->amount_retention ?? 0, 2) }}</b> บาท<br>
                
            <span>[{{ in_array('FARMER', $selectedTypes) ? '✓' : ' ' }}] เกษตรกร</span> 
                จำนวน <b>{{ number_format($approval->amount_farmer ?? 0, 2) }}</b> บาท<br>
                
            <span>[{{ in_array('Welcome', $selectedTypes) ? '✓' : ' ' }}] Welcome</span> 
                จำนวน <b>{{ number_format($approval->amount_welcome ?? 0, 2) }}</b> บาท
    </div>      
                Fleet <span class="dotted-line">{{ $approval->fleet_amount ?? '-' }} </span> บาท 
                หักประกัน <span class="dotted-line">{{ $approval->insurance_deduct ?? '-' }} </span> บาท 
                ใช้จริง <span class="dotted-line">{{ $approval->insurance_used ?? '-' }} </span> บาท<br>
                Kickback <span class="dotted-line">{{ $approval->kickback_amount ?? '-' }} </span> บาท<br>
                Com F/N <span class="dotted-line">{{ $approval->com_option ?? '-' }} </span>
                จำนวน <span class="dotted-line">{{ $approval->com_fn_amount ?? '-' }} </span> บาท<br>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border: 1px solid black;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid black; width: 33%;">รายการของแถม</th>
                <th style="border: 1px solid black; width: 33%;">รายการของแถมเกิน</th>
                <th style="border: 1px solid black; width: 34%;">รายการซื้อเพิ่ม</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid black; vertical-align: top;">
                    @foreach($items as $item)
                        <div>{{ $loop->iteration }}. {{ $item->name }}</div>
                    @endforeach
                </td>
                <td style="border: 1px solid black; vertical-align: top;">
                    </td>
                <td style="border: 1px solid black; vertical-align: top;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>ทะเบียน + พรบ</span>
                        <span>7,500</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="bordered" style="margin-top: 15px;">
        <tr class="text-center bold">
            <td width="33%">แคมเปญที่มี</td>
            <td width="33%">แคมเปญที่ใช้</td>
            <td width="34%">Commercial 30,000 บาท</td>
        </tr>
        <tr>
            <td>
                แคมเปญ = 75,000<br>
                GE = 15,000<br>
                RE = 10,000<br><br>
                <span class="bold">รวม = 100,000</span>
            </td>
            <td>
                ส่วนลด (เงินสด/ดาวน์) = 63,000<br>
                ซื้อเพิ่ม = 12,000<br>
                หัก L = 25,000<br><br>
                <span class="bold">รวม = 100,000</span>
            </td>
            <td>
                <table style="margin: 0; border: none;">
                    <tr style="border: none;"><td style="border: none;">รายการแต่ง</td><td style="border: none;">มูลค่า</td></tr>
                    <tr style="border: none;"><td style="border: none; height: 80px;"></td><td></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        รับรถจ่ายเงินสด/ดาวน์ = 264,750 - 63,000 = <span class="bold" style="font-size: 16px;">201,750</span><br>
        จ่ายของแต่ง = <span class="dotted-line">....................</span> รวมทั้งหมด = <span class="dotted-line">....................</span>
    </div>

    <div style="display: flex; justify-content: space-around; margin-top: 30px; text-align: center;">
        <div>
            เงินแคมเปญคงเหลือ <span class="dotted-line">ไม่เกิน</span> บาท<br><br>
            ....................................................<br>
            ผู้ขออนุมัติ
        </div>
        <div>
            เงินของตกแต่งคงเหลือ <span class="dotted-line">ไม่เกิน</span> บาท<br><br>
            ....................................................<br>
            ที่ปรึกษาการขาย
        </div>
    </div>
</div>

</body>
</html>