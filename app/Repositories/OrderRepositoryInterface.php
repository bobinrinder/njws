<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function fetchAll(): Collection;

    public function saveCollection(Collection $orders): Collection;

    public function fetchAllAndSave(): Collection;

    public function verifyAndProcessAll(): Collection;

    public function submitProcessingResults(Collection $orders): Collection;
}
