<?php 
namespace App\Shipping;

use App\Product;

class WeightBasedShipping implements ShippingStrategy
{
    public function calculateShippingCost(Product $product): float
    {
        $coefficientPerKg = 11.0; // rate per kilogram
        return $product->getWeight() * $coefficientPerKg;
    }
}