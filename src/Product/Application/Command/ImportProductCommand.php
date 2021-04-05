<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Application\Command;

use PrettyLittleThing\Product\Domain\Model\NormalPriceNotFloatException;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException;

class ImportProductCommand
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
     * @var string
     */
    private $normalPrice;

    /**
     * @var string
     */
    private $specialPrice;

    /**
     * CreateProductCommand constructor.
     * @param string $sku
     * @param string $description
     * @param string $normalPrice
     * @param string|null $specialPrice
     */
    public function __construct(
        string $sku,
        string $description,
        string $normalPrice,
        string $specialPrice = null
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
     * @return string
     */
    public function getNormalPrice(): string
    {
        return $this->normalPrice;
    }

    /**
     * @return null|string
     */
    public function getSpecialPrice(): ?string
    {
        return $this->specialPrice;
    }
}