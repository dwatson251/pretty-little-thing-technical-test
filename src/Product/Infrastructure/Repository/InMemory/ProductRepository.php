<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Infrastructure\Repository\InMemory;

use PrettyLittleThing\Product\Domain\Model\Product;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;
use PrettyLittleThing\Product\Domain\Model\ProductUpdate;
use PrettyLittleThing\Product\Infrastructure\Repository\ProductSkuNotFoundException;

class ProductRepository implements ProductRepositoryInterface
{
    protected $products = [];

    public function create(Product $product): void
    {
        $this->products[$product->getSku()] = $product;
    }

    /**
     * @param string $sku
     * @return Product
     *
     * @throws ProductSkuNotFoundException
     */
    public function findBySku(string $sku): Product
    {
        try {
            $product = $this->products[$sku];
        } catch (\Exception $e) {
            throw new ProductSkuNotFoundException($sku);
        }

        return $product;
    }

    /**
     * @param string $sku
     * @param ProductUpdate $productUpdate
     *
     * @throws ProductSkuNotFoundException
     */
    public function update(string $sku, ProductUpdate $productUpdate): void
    {
       $product = $this->findBySku($sku);

        $product->setDescription($productUpdate->getDescription());
        $product->setNormalPrice($productUpdate->getNormalPrice());
        $product->setSpecialPrice($productUpdate->getSpecialPrice());
    }
}