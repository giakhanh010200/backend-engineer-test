<?php

namespace App\Shipping;

use App\Product;

class ProductTypeFeeDecorator implements ShippingStrategy
{
    private ShippingStrategy $baseStrategy;
    private array $productTypeFees;

    public function __construct(ShippingStrategy $baseStrategy, array $productTypeFees)
    {
        $this->baseStrategy = $baseStrategy;
        $this->productTypeFees = $productTypeFees;
    }

    public function calculateShippingCost(Product $product): float
    {
        $baseCost = $this->baseStrategy->calculateShippingCost($product);
        $productType = $product->getType();

        if ($productType && isset($this->productTypeFees[$productType])) {
            return max($baseCost, $this->productTypeFees[$productType]);
        }

        return $baseCost;
    }
}