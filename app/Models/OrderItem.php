<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Order;
use App\Models\Item;

class OrderItem extends Model
{
    use HasFactory;

    /** The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return BelongsTo|Order
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * @return BelongsTo|Item
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }

    /**
     * Checks and sets validity of order item based on available quantity.
     *
     * @return void
     */
    public function setValidity()
    {
        if ($this->item) {
            if ($this->quantity <= $this->item->getQuantityForDate(new \Carbon\Carbon($this->order->shipping_date))) {
                $this->is_valid = true;
                $this->processing_result = 'OK';
            } else {
                $this->processing_result = 'Not enough quantity available in time!';
            }
        } else {
            $this->processing_result = 'Cannot find item in any warehouse!';
        }
        $this->save();
    }
}
