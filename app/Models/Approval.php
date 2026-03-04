<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'customer_address','customer_subdistrict', 
        'customer_district', 'customer_province', 'customer_phone',
        'customer_email', 'car_model', 'car_color', 'car_options', 'car_price',
        'plus_head', 'fn', 'down_percent', 'down_amount', 'finance_amount',
        'installment_per_month', 'installment_months', 'interest_rate',
        'fleet_amount', 'kickback_amount', 'campaigns_available', 
        'campaigns_used', 'free_items', 'free_items_over', 'extra_purchase_items', 
        'decoration_amount', 'over_campaign_amount', 'over_decoration_amount', 'over_reason',
        'remark', 'sale_type_options', 'sc_signature_data', 'sale_com_signature_data',
        'is_commercial_30000', 'group_id', 'version', 'status', 
        'created_by', 'sales_name', 'sales_user_id'
    ];
    // app/Models/Approval.php
    protected $casts = [
        'options' => 'array', // บังคับให้ Laravel แปลงข้อมูล JSON เป็น Array อัตโนมัติ
    ];

    public function documents()
        {
            return $this->hasMany(ApprovalDocument::class);
        }

}