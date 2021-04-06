<?php


namespace PrettyLittleThing\Product\Application\CommandHandler;


use PrettyLittleThing\Product\Application\Command\CreateProductCommand;
use PrettyLittleThing\Product\Domain\Model\Product;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;
use PrettyLittleThing\Product\Domain\Response\ProductCreatedResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateProductCommandHandler implements MessageHandlerInterface
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
     * @param CreateProductCommand $command
     * @return ProductCreatedResponse
     */
    public function __invoke(CreateProductCommand $command)
    {
        $this->productRepository->create(
            new Product(
                $command->getSku(),
                $command->getDescription(),
                $command->getNormalPrice(),
                $command->getSpecialPrice()
            )
        );

        return new ProductCreatedResponse();
    }
}