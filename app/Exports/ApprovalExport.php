<?php

namespace App\Exports;

use App\Models\Approval;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApprovalExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    // ไม่ต้องรับ ID แล้ว เพราะเราจะเอา "ทั้งหมด"

    public function collection()
    {
        // ดึงข้อมูลทั้งหมด (เรียงจากล่าสุดไปเก่าสุด)
        return Approval::orderBy('created_at', 'Asc')->get()->map(function($approval) {
            return [
                $approval->id,
                $approval->created_at->format('d/m/Y'), // วันที่
                $approval->customer_name,
                $approval->customer_phone,
                $approval->customer_email,
                $approval->car_model,
                $approval->car_color,
                number_format($approval->car_price, 2),
                number_format($approval->down_amount, 2),
                number_format($approval->finance_amount, 2),
                $approval->installment_months ?? '-' . ' งวด',
                $approval->status, 
                $approval->sales_name ?? '-',
            ];
        });
    }

    // หัวตาราง (Header)
    public function headings(): array
    {
        return [
            'NO.', 
            'วันที่ทำรายการ', 
            'ชื่อลูกค้า', 
            'เบอร์โทร',
            'อีเมล', 
            'รุ่นรถ', 
            'สีรถ', 
            'ราคารถ', 
            'เงินดาวน์', 
            'ยอดจัด', 
            'จำนวนงวด', 
            'สถานะ',
            'ผู้ขาย'
        ];
    }

    // จัดตัวหนาบรรทัดแรก
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}