<?php 
namespace App;

class Order
{
    private array $products = [];

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function calculateTotalPrice($shippingContext): float
    {
        $totalPrice = 0.0;
        foreach ($this->products as $product) {
            $totalPrice += $product->getAmazonPrice() + $shippingContext->calculateShippingCost($product);
        }

        return $totalPrice;
    }
}

