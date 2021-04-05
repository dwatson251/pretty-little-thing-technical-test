<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Domain\Model;

use PrettyLittleThing\Product\Infrastructure\Repository\ProductSkuNotFoundException;

interface ProductRepositoryInterface
{
    /**
     * @param string $sku
     * @return Product
     *
     * @throws ProductSkuNotFoundException
     */
    public function findBySku(string $sku): Product;

    public function create(Product $product): void;

    public function update(string $sku, ProductUpdate $productUpdate): void;
}