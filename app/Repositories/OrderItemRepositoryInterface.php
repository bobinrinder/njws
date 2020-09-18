<?php

namespace App\Repositories;

use App\Models\OrderItem;
use Illuminate\Support\Collection;

interface OrderItemRepositoryInterface
{
    public function saveCollection(Collection $orderItems, int $orderId): Collection;

    public function setValidity(Collection $orderItems): Collection;

    public function saveCollectionAndSetValidity(Collection $orderItems, int $orderId): Collection;
}
