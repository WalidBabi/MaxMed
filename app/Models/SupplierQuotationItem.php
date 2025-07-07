<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierQuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_quotation_id',
        'supplier_inquiry_item_id',
        'product_id',
        'product_name',
        'product_description',
        'unit_price',
        'currency',
        'shipping_cost',
        'size',
        'notes',
        'quantity',
        'attachments',
        'sort_order',
    ];

    protected $casts = [
        'attachments' => 'array',
        'unit_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'quantity' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class, 'supplier_quotation_id');
    }

    public function inquiryItem(): BelongsTo
    {
        return $this->belongsTo(SupplierInquiryItem::class, 'supplier_inquiry_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
