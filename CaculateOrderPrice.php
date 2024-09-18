<?php 


// Product class
class Product {
    private float $amazon_price;
    private float $weight;
    private float $width;
    private float $height;
    private float $depth;
    private ?string $type;

    public function __construct(float $amazon_price, float $weight, float $width, float $height, float $depth, ?string $type = null)
    {
        $this->amazon_price = $amazon_price;
        $this->weight = $weight;
        $this->width = $width;
        $this->height = $height;
        $this->depth = $depth;
        $this->type = $type;
    }

    public function getAmazonPrice():float 
    {
        return $this->amazon_price;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getDepth(): float
    {
        return $this->depth;
    }

    public function getVolume(): float
    {
        return $this->width * $this->height * $this->depth;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
// interface shipping strategy
interface ShippingStrategy
{
    public function calculateShippingCost(Product $product): float;
}

// product shipping strategy

// calculate base on product weight
class WeightBasedShipping implements ShippingStrategy
{
    public function calculateShippingCost(Product $product): float
    {
        $coefficientPerKg = 11.0; // rate per kilogram
        return $product->getWeight() * $coefficientPerKg;
    }
}


// calculate base on dimension
class DimensionBasedShipping implements ShippingStrategy
{
    public function calculateShippingCost(Product $product): float
    {
        $coefficientPerCubicMeter = 11.0; //rate per cubic meter
        return $product->getVolume() * $coefficientPerCubicMeter;
    }
}

// combined shipping strategy - max shipping fee between product weight and dimension and type
class CombinedShippingStrategy implements ShippingStrategy
{
    private ShippingStrategy $weightStrategy;
    private ShippingStrategy $dimensionStrategy;

    public function __construct(ShippingStrategy $weightStrategy, ShippingStrategy $dimensionStrategy)
    {
        $this->weightStrategy = $weightStrategy;
        $this->dimensionStrategy = $dimensionStrategy;
    }

    public function calculateShippingCost(Product $product): float
    {
        $weightCost = $this->weightStrategy->calculateShippingCost($product);
        $dimensionCost = $this->dimensionStrategy->calculateShippingCost($product);

        return max($weightCost, $dimensionCost);
    }
}

// product type fee decorator - handle product type 
class ProductTypeFeeDecorator implements ShippingStrategy
{
    private ShippingStrategy $wrappedStrategy;
    private array $productTypeFees;

    public function __construct(ShippingStrategy $strategy, array $productTypeFees)
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

// class order handle multiple value
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

$productSmartphone = new Product(800.0, 0.003, 0.15, 0.075, 0.005, 'smartphone');
$productDiamondRing = new Product(5000.0, 0.0001, 0.02 , 0.02, 0.02, 'diamond_ring');
$hiddenBox = new Product(1000.0, 1, 1.3 , 1.2, 0.3, null);

// Define the additional product type fees
$productTypeFees = [
    'diamond_ring' => 50.0,
];

// Create an order and add multiple products
$order = new Order();
$order->addProduct($productSmartphone);
$order->addProduct($productDiamondRing);
$order->addProduct($hiddenBox);

// Use CombinedShippingStrategy (max of weight and dimensions), wrapped by the ProductTypeFeeDecorator
$combinedStrategy = new CombinedShippingStrategy(new WeightBasedShipping(), new DimensionBasedShipping());
$shippingContext = new ShippingContext(new ProductTypeFeeDecorator($combinedStrategy, $productTypeFees));

// Calculate the total shipping cost for the order
$totalShippingCost = $order->calculateTotalPrice($shippingContext);
echo "Total price cost for the order: $" . $totalShippingCost . PHP_EOL;