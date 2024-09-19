<?php

namespace App;

class Product {
    private float $amazon_price;
    private float $weight;
    private float $width;
    private float $height;
    private float $depth;
    private ?string $type;

    public function __construct(
        float $amazon_price, 
        float $weight, 
        float $width, 
        float $height, 
        float $depth, 
        ?string $type = null
    )
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