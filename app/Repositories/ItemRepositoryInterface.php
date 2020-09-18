<?php

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Support\Collection;

interface ItemRepositoryInterface
{
    public function fetchAll(): Collection;

    public function saveCollection(Collection $items): Collection;

    public function fetchAllAndSave(): Collection;
}
