<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\OrderItem;
use App\Models\OrderSystem;

class Order extends Model
{
    use HasFactory;

    /** The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return HasMany|Collection|OrderItem[]
     */
    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem');
    }

    /**
     * @return BelongsTo|OrderSystem
     */
    public function orderSystem()
    {
        return $this->belongsTo('App\Models\OrderSystem');
    }

    /**
     * Checks and sets validity of order based on validity
     * of corresponding order items.
     *
     * @return void
     */
    public function setValidity()
    {
        if ($this->orderItems->count() > 0 && $this->orderItems->where('is_valid', '=', true)->count() === $this->orderItems->count()) {
            $this->status = 'processed';
        } else {
            $this->status = 'failed';
        }
        $this->save();
    }
}
