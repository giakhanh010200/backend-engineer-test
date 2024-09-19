<?php
namespace App\Shipping;

use App\Product;

class CombinedShippingStrategy implements ShippingStrategy
{
    private ShippingStrategy $weightBasedStrategy;
    private ShippingStrategy $dimensionBasedStrategy;

    public function __construct(ShippingStrategy $weightBasedStrategy, ShippingStrategy $dimensionBasedStrategy)
    {
        $this->weightBasedStrategy = $weightBasedStrategy;
        $this->dimensionBasedStrategy = $dimensionBasedStrategy;
    }

    public function calculateShippingCost(Product $product): float
    {
        $weightCost = $this->weightBasedStrategy->calculateShippingCost($product);
        $dimensionCost = $this->dimensionBasedStrategy->calculateShippingCost($product);

        return max($weightCost, $dimensionCost);
    }
}