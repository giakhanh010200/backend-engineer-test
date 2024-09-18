<?php

use PHPUnit\Framework\TestCase;
use App\Product;
use App\WeightBasedShipping;
use App\DimensionBasedShipping;
use App\CombinedShippingStrategy;
use App\ProductTypeFeeDecorator;
use App\Order;

class ShippingTest extends TestCase
{
    // 1. Test weight-based shipping fee
    public function testWeightBasedShipping()
    {
        $product = new Product(100.0, 5.0, 0.1, 0.1, 0.1, null); // 5kg
        $weightStrategy = new WeightBasedShipping();

        $shippingFee = $weightStrategy->calculateShippingCost($product);

        $this->assertEquals(55.0, $shippingFee, "The weight-based shipping fee.");
    }

    // 2. Test dimension-based shipping fee
    public function testDimensionBasedShipping()
    {
        $product = new Product(100.0, 1, 1.0, 0.5, 0.5, null); // Volume: 1 * 0.5 * 0.5 = 0.25 cubic meters
        $dimensionStrategy = new DimensionBasedShipping();

        $shippingFee = $dimensionStrategy->calculateShippingCost($product);

        $this->assertEquals(2.75, $shippingFee, "The dimension-based shipping fee.");
    }

    // 3. Test combined shipping fee (max of weight and dimensions)
    public function testCombinedShipping()
    {
        $product = new Product(100.0, 2.0, 0.5, 0.5, 0.5, null); // Weight: 2kg, Volume: 0.5 * 0.5 * 0.50 = 0.125 cubic meters
        $combinedStrategy = new CombinedShippingStrategy(new WeightBasedShipping(), new DimensionBasedShipping());

        $shippingFee = $combinedStrategy->calculateShippingCost($product);

        $this->assertEquals(22, $shippingFee, "The combined shipping fee should be the maximum of weight and dimension-based fees.");
    }

    // 4. Test product type fee decorator (with product type)
    public function testProductTypeFeeWithProductType()
    {
        $product = new Product(100.0, 1.0, 0.1, 0.1, 0.1, 'smartphone'); // 1kg
        $combinedStrategy = new CombinedShippingStrategy(new WeightBasedShipping(), new DimensionBasedShipping());

        // Product type fee for 'smartphone' is 15
        $productTypeFees = ['smartphone' => 15.0];

        $decoratedStrategy = new ProductTypeFeeDecorator($combinedStrategy, $productTypeFees);

        $shippingFee = $decoratedStrategy->calculateShippingCost($product);

        $this->assertEquals(15.0, $shippingFee, "The shipping fee should include the product-type fee.");
    }

    // 5. Test product type fee decorator (with null product type)
    public function testProductTypeFeeWithNullType()
    {
        $product = new Product(100.0, 1.0, 0.1, 0.1, 0.1, null); // 1kg, no product type
        $combinedStrategy = new CombinedShippingStrategy(new WeightBasedShipping(), new DimensionBasedShipping());

        // No product type fee for null
        $productTypeFees = ['smartphone' => 15.0];

        $decoratedStrategy = new ProductTypeFeeDecorator($combinedStrategy, $productTypeFees);

        $shippingFee = $decoratedStrategy->calculateShippingCost($product);

        $this->assertEquals(11.0, $shippingFee, "The shipping fee for a product with no type should be the base fee.");
    }

    // 6. Test total shipping cost for an order with multiple products
    public function testOrderTotalPrice()
    {
        $productSmartphone = new Product(800.0, 0.003, 0.15, 0.075, 0.005, 'smartphone');
        $productDiamondRing = new Product(5000.0, 0.0001, 0.02 , 0.02, 0.02, 'diamond_ring');
        $hiddenBox = new Product(1000.0, 1, 1.3 , 1.2, 0.3, null);

        // Define the additional product type fees
        $productTypeFees = [
            'smartphone' => 15.0,
            'diamond_ring' => 50.0,
        ];

        // Use CombinedShippingStrategy (max of weight and dimensions), wrapped by the ProductTypeFeeDecorator
        $combinedStrategy = new CombinedShippingStrategy(new WeightBasedShipping(), new DimensionBasedShipping());
        $decoratedStrategy = new ProductTypeFeeDecorator($combinedStrategy, $productTypeFees);

        // Create an order and add multiple products
        $order = new Order();
        $order->addProduct($productSmartphone);
        $order->addProduct($productDiamondRing);
        $order->addProduct($hiddenBox);

        // Calculate the total shipping cost for the order
        $totalShippingCost = $order->calculateTotalPrice($decoratedStrategy);

        // Expected total shipping cost:
        // - Smartphone: max(0.3 * 11, (0.15 * 0.075 * 0.005 ) * 11) = max(3.3, 0.0006) = 15 (due to product type fee)
        // - Diamond ring: max(0.0001 * 11, (0.02 * 0.02 * 0.02) * 11) = max(0.0011, 0.000008) = 50 (due to product type fee)
        // - hiddenBox : max(1.0 * 11, (1.3 * 1.2 * 0.3) * 11) = max(11, 5.148) = 11
        $expectedTotalShipping = 800 + 15 + 5000 + 50 + 1000 + 11 ;

        $this->assertEquals($expectedTotalShipping, $totalShippingCost, "The total shipping cost for the order should be calculated correctly.");
    }
}
