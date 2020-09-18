<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\ItemRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    private $items;

    /**
     * ItemRepository constructor.
     *
     * @param Item $model
     */
    public function __construct(Item $model)
    {
        parent::__construct($model);
    }

    /**
     * Fetches all items from all warehouse systems.
     *
     * @return Collection
     */
    public function fetchAll(): Collection
    {
        $warehouseSystemObj = new \App\Models\WarehouseSystem;
        $warehouseSystems = $warehouseSystemObj->all();

        $items = collect([]);

        foreach ($warehouseSystems as $warehouseSystem) {
            // fetch items for each item system
            $response = Http::get($warehouseSystem->get_endpoint_url);
            $newItems = json_decode($response->body());

            // we add the warehouse system to uniquely identify an item
            // by its item id given by the external item system
            // and its warehouse system id
            foreach ($newItems as &$newItem) {
                $newItem->warehouse_system_id = $warehouseSystem->id;
            }

            // merge to return all items
            $items = $items->merge($newItems);
        }

        return $items;
    }

    /**
    * Saves given items to DB.
    *
    * @param Collection $items
    * @return Collection
    */
    public function saveCollection(Collection $items): Collection
    {
        foreach ($items as $item) {
            $this->updateOrCreate(
                [
                    'id' => $item->id,
                    'warehouse_system_id' => $item->warehouse_system_id,
                ],
                [
                    'available_from_date' => new \Carbon\Carbon($item->availableFromDate),
                    'sku' => $item->sku,
                    'product_url' => $item->productUrl,
                    'quantity' => $item->quantity,
                    'upc' => $item->upc,
                    'unit_price' => $item->unitPrice,
                    'recommended_sales_price' => $item->recommendedSalesPrice,
                    'product_name' => $item->productName,
                ]
            );
        }

        return $items;
    }

    /**
     * Fetches all items from all warehouse systems and saves them to the DB.
     *
     * @return Collection
     */
    public function fetchAllAndSave(): Collection
    {
        $this->items = $this->fetchAll();
        $this->saveCollection($this->items);
        return $this->items;
    }
}
