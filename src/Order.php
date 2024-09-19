<?php 
namespace App;

use App\Shipping\ShippingContext;
class Order
{
    private array $products = [];

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function calculateTotalPrice(ShippingContext $shippingContext): float
    {
        $totalPrice = 0.0;
        foreach ($this->products as $product) {
            $totalPrice += $product->getAmazonPrice() + $shippingContext->calculateShipping($product);
        }

        return $totalPrice;
    }
}

