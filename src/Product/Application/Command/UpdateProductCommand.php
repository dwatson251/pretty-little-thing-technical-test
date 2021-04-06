<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Application\Command;


class UpdateProductCommand
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
     * @var int
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
}