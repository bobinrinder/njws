<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

class Item extends Model
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
     * Get available quantity of item for given date.
     *
     * @param \Carbon\Carbon $date
     * @return float
     */
    public function getQuantityForDate(\Carbon\Carbon $date): float
    {
        return $this->where([
            ['id', '=', $this->id],
            ['available_from_date', '>', $date]
        ])->pluck('quantity')->sum();
    }
}
