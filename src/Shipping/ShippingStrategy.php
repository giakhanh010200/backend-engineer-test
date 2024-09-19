<?php
namespace App\Shipping;

use App\Product;

interface ShippingStrategy
{
    public function calculateShippingCost(Product $product): float;
}