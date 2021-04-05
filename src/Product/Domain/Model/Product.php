<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Domain\Model;

class Product
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
     * @var int
     */
    private $normalPrice;

    /**
     * @var null|int
     */
    private $specialPrice;

    public function __construct(
        string $sku,
        string $description,
        int $normalPrice,
        int $specialPrice = null
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
     * @return int
     */
    public function getNormalPrice(): int
    {
        return $this->normalPrice;
    }

    /**
     * @return null|int
     */
    public function getSpecialPrice(): ?int
    {
        return $this->specialPrice;
    }

    /**
     * @param string $sku
     * @return Product
     */
    public function setSku(string $sku): Product
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param int $normalPrice
     * @return Product
     */
    public function setNormalPrice(int $normalPrice): Product
    {
        $this->normalPrice = $normalPrice;
        return $this;
    }

    /**
     * @param int|null $specialPrice
     * @return Product
     */
    public function setSpecialPrice(?int $specialPrice): Product
    {
        $this->specialPrice = $specialPrice;
        return $this;
    }
}