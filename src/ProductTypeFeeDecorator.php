<?php
namespace App;
use App\CombinedShippingStrategy;

class ProductTypeFeeDecorator
{
    private CombinedShippingStrategy $wrappedStrategy;
    private array $productTypeFees;

    public function __construct(CombinedShippingStrategy $strategy, array $productTypeFees)
    {
        $this->wrappedStrategy = $strategy;
        $this->productTypeFees = $productTypeFees;
    }

    public function calculateShippingCost(Product $product): float
    {
        // calculate the base shipping fee
        $baseShippingFee = $this->wrappedStrategy->calculateShippingCost($product);

        // get the product type; if it's null, don't apply any type-specific fee
        $productType = $product->getType();
        if ($productType === null) {
            return $baseShippingFee;
        }

        $productTypeFee = $this->productTypeFees[$productType] ?? 0.0;

        return max($baseShippingFee, $productTypeFee);
    }
}


