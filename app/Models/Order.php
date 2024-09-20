<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "total",
        "notes",
        // "products",
        "status",
        "order_status",
        "payment_type",
        "payment_status",
        "transaction_id",
        "employee_id",
        "customer_id",
        "dining_table_id"
    ];

    protected $casts = [
        'products' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function dining_table()
    {
        return $this->belongsTo(DiningTable::class);
    }
}
