<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Infrastructure\Repository\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use PrettyLittleThing\Product\Domain\Model\Product;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;
use PrettyLittleThing\Product\Domain\Model\ProductUpdate;
use PrettyLittleThing\Product\Infrastructure\Repository\ProductSkuNotFoundException;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $sku
     * @return Product
     *
     * @throws ProductSkuNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySku(string $sku): Product
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select("products")
            ->from(Product::class,'products')
            ->where('products.sku = :sku')
            ->setParameter('sku', $sku);

        $query = $queryBuilder->getQuery();

        $result = $query->getOneOrNullResult();

        if (null === $result) {
            throw new ProductSkuNotFoundException($sku);
        }

        return $result;
    }

    public function create(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /**
     * @param string $sku
     * @param ProductUpdate $productUpdate
     *
     * @throws ProductSkuNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function update(string $sku, ProductUpdate $productUpdate): void
    {
        $product = $this->findBySku($sku);

        $product->setDescription($productUpdate->getDescription());
        $product->setNormalPrice($productUpdate->getNormalPrice());
        $product->setSpecialPrice($productUpdate->getSpecialPrice());

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}