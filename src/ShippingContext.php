<?php 

class ShippingContext
{
    private ShippingStrategy $strategy;

    public function __construct(ShippingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(ShippingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function calculateShipping(Product $product): float
    {
        return $this->strategy->calculateShippingCost($product);
    }
}