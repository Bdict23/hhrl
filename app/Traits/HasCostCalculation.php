<?php

namespace App\Traits;

use App\Models\Item;

trait HasCostCalculation
{
    /**
     * Calculate current cost with dynamic parameters
     *
     * @param Item|null $item The item to fetch cost for
     * @param int|null $takeCount Number of cost records to consider (default: 2)
     * @param int|null $monthsBack How many months back to check (default: 1)
     * @return float
     */
    public function calculateItemCost(?Item $item, ?int $takeCount = null, ?int $monthsBack = null): float
    {
        if (!$item || !$item->exists) {
            return 0.0;
        }

        // Use default values if parameters are null
        $takeCount = $takeCount ?? 2;
        $monthsBack = $monthsBack ?? 1;

        // Fetch the latest cost data based on the dynamic time range
        $recentCosts = $item->priceLevels()
            ->where('price_type', 'cost')
            ->where('created_at', '>=', now()->subMonths($monthsBack))
            ->orderBy('created_at', 'desc')
            ->take($takeCount)
            ->get();

        return $recentCosts->count() === $takeCount
            ? $recentCosts->avg('amount') // If we have enough records, return the average
            : ($recentCosts->first()?->amount ?? 0); // Otherwise, return the latest cost
    }
}
