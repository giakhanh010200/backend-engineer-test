<?php
namespace App;

class DimensionBasedShipping
{
    public function calculateShippingCost(Product $product): float
    {
        $coefficientPerCubicMeter = 11.0; //rate per cubic meter
        return $product->getVolume() * $coefficientPerCubicMeter;
    }
}