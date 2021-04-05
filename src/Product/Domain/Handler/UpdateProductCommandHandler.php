<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Domain\Handler;

use PrettyLittleThing\Product\Application\Command\ImportProductCommand;
use PrettyLittleThing\Product\Application\Command\UpdateProductCommand;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;
use PrettyLittleThing\Product\Domain\Model\ProductUpdate;
use PrettyLittleThing\Product\Domain\Response\ProductUpdatedResponse;
use PrettyLittleThing\Product\Infrastructure\Repository\ProductSkuNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateProductCommandHandler implements MessageHandlerInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;


    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param UpdateProductCommand $command
     *
     * @return ProductUpdatedResponse
     */
    public function __invoke(UpdateProductCommand $command)
    {
        // Can instead dispatch an update command
        $this->productRepository->update(
            $command->getSku(),
            new ProductUpdate(
                $command->getDescription(),
                $command->getNormalPrice(),
                $command->getSpecialPrice()
            )
        );

        return new ProductUpdatedResponse();
    }
}