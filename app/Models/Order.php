<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'order_source_id',
        'branch_id',
        'reseller_order',
        'reseller_id',
        'customer_name',
        'percentage_discount',
        'total_discount',
        'total_line_items',
        'total_price',
    ];

    /**
     * @return BelongsTo
     */
    public function orderSource()
    {
        return $this->belongsTo(OrderSource::class);
    }

    /**
     * @return BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
