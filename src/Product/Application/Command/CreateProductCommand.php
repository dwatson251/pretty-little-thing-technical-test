<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Application\Command;

class CreateProductCommand
{
    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $normalPrice;

    /**
     * @var float
     */
    private $specialPrice;

    /**
     * CreateProductCommand constructor.
     * @param string $sku
     * @param string $description
     * @param float $normalPrice
     * @param float|null $specialPrice
     */
    public function __construct(
        string $sku,
        string $description,
        float $normalPrice,
        float $specialPrice = null
    ) {
        $this->sku = $sku;
        $this->description = $description;
        $this->normalPrice = $normalPrice;
        $this->specialPrice = $specialPrice;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getNormalPrice(): float
    {
        return $this->normalPrice;
    }

    /**
     * @return null|float
     */
    public function getSpecialPrice(): ?float
    {
        return $this->specialPrice;
    }
}