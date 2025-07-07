<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInquiryItem extends Model
{
    protected $fillable = [
        'supplier_inquiry_id',
        'product_id',
        'product_name',
        'product_description',
        'product_category',
        'product_brand',
        'product_specifications',
        'specifications',
        'size',
        'quantity',
        'requirements',
        'notes',
        'sort_order'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    /**
     * Get the supplier inquiry that owns this item
     */
    public function supplierInquiry(): BelongsTo
    {
        return $this->belongsTo(SupplierInquiry::class);
    }

    /**
     * Get the product associated with this item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
