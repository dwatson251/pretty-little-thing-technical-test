<?php


namespace PrettyLittleThing\Product\Domain\Model;


class ProductUpdate
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $normalPrice;

    /**
     * @var null|float
     */
    private $specialPrice;

    public function __construct(
        string $description,
        float $normalPrice,
        float $specialPrice = null
    ) {
        $this->description = $description;
        $this->normalPrice = $normalPrice;
        $this->specialPrice = $specialPrice;
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
    public function getNormalPrice(): int
    {
        return $this->normalPrice;
    }

    /**
     * @return null|float
     */
    public function getSpecialPrice(): ?int
    {
        return $this->specialPrice;
    }
}