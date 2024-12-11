<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    const STATUS_NEW=0;
    const STATUS_ACTIVATED=1;
    const STATUS_INFORMATION_PROVIDED=2;
    const STATUS_CODE_AWAITING=3;
    const STATUS_CODE_SENT=4;
    const STATUS_FINISHED=5;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'activation_code',
        'product_alias',
        'price',
        'email',
        'activated_at',
        'completed_at',
        'status',
        'auth_info'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => 'integer',
        'auth_info' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_alias', 'alias');
    }
}
