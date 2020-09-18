<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\OrderItemRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    private $orderItems;

    /**
     * OrderItemRepository constructor.
     *
     * @param Order $model
     */
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

    /**
     * Saves given order items to DB.
     *
     * @param Collection $orderItems
     * @param int $orderId
     * @return Collection
     */
    public function saveCollection(Collection $orderItems, int $orderId): Collection
    {
        $newOrderItems = collect([]);

        foreach ($orderItems as $orderItem) {
            $newOrderItem = $this->updateOrCreate(
                [
                    'order_id' => $orderId,
                    'item_id' => $orderItem->id,
                ],
                [
                    'quantity' => $orderItem->quantity,
                ]
            );
            $newOrderItems->push($newOrderItem);
        }

        return $newOrderItems;
    }

    /**
     * Determines and sets validity of all given order items.
     *
     * @param Collection $orderItems
     * @return Collection
     */
    public function setValidity(Collection $orderItems): Collection
    {
        foreach ($orderItems as $orderItem) {
            $orderItem->setValidity();
        }
        return $orderItems;
    }

    /**
     * Saves given order items to DB and determines and sets their validity.
     *
     * @param Collection $orderItems
     * @param int $orderId
     * @return Collection
     */
    public function saveCollectionAndSetValidity(Collection $orderItems, int $orderId): Collection
    {
        $this->orderItems = $this->saveCollection($orderItems, $orderId);
        $this->orderItems = $this->setValidity($this->orderItems);
        return $this->orderItems;
    }
}
