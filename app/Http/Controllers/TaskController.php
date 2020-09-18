<?php

namespace App\Http\Controllers;

use App\Repositories\ItemRepositoryInterface;
use App\Repositories\OrderRepositoryInterface;

class TaskController extends Controller
{
    private $orderRepository;
    private $itemRepository;

    /**
     * TaskController constructor.
     *
     */
    public function __construct(OrderRepositoryInterface $orderRepository, ItemRepositoryInterface $itemRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * Fetches orders and items, saves both, processes them
     * locally and then submits the result to the order systems.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleTask()
    {
        // step 1: fetch items from warehouses
        $items = $this->itemRepository->fetchAllAndSave();

        // step 2: fetch orders from order systems
        $orders = $this->orderRepository->fetchAllAndSave();

        // step 3: verify and save processing result of all orders
        $orders = $this->orderRepository->verifyAndProcessAll();

        // step 4: submit processing results back to order systems
        $orders = $this->orderRepository->submitProcessingResults($orders);

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }
}
