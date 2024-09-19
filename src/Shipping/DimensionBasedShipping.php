<?php
namespace App\Shipping;

use App\Product;

class DimensionBasedShipping implements ShippingStrategy
{
    public function calculateShippingCost(Product $product): float
    {
        $coefficientPerCubicMeter = 11.0; //rate per cubic meter
        return $product->getVolume() * $coefficientPerCubicMeter;
    }
}