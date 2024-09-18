<?php 
namespace App;

class WeightBasedShipping
{
    public function calculateShippingCost(Product $product): float
    {
        $coefficientPerKg = 11.0; // rate per kilogram
        return $product->getWeight() * $coefficientPerKg;
    }
}