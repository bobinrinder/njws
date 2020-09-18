<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

class OrderSystem extends Model
{
    use HasFactory;

    /**
     * @return HasMany|Collection|Order[]
     */
    public function orders()
    {
        $this->hasMany('App\Models\Order');
    }
}
