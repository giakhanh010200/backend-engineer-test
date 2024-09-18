<?php
namespace App;

use App\DimensionBasedShipping;
use App\WeightBasedShipping;

class CombinedShippingStrategy
{
    private WeightBasedShipping $weightStrategy;
    private DimensionBasedShipping $dimensionStrategy;

    public function __construct(WeightBasedShipping $weightStrategy, DimensionBasedShipping $dimensionStrategy)
    {
        $this->weightStrategy = $weightStrategy;
        $this->dimensionStrategy = $dimensionStrategy;
    }

    public function calculateShippingCost(Product $product): float
    {
        $weightCost = $this->weightStrategy->calculateShippingCost($product);
        $dimensionCost = $this->dimensionStrategy->calculateShippingCost($product);

        return max($weightCost, $dimensionCost);
    }
}