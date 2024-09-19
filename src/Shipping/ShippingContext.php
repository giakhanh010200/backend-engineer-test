<?php

namespace App\Shipping;

use App\Product;

class ShippingContext
{
    private ShippingStrategy $shippingStrategy;

    public function __construct(ShippingStrategy $shippingStrategy)
    {
        $this->shippingStrategy = $shippingStrategy;
    }

    public function setStrategy(ShippingStrategy $shippingStrategy): void
    {
        $this->shippingStrategy = $shippingStrategy;
    }

    public function calculateShipping(Product $product): float
    {
        return $this->shippingStrategy->calculateShippingCost($product);
    }
}