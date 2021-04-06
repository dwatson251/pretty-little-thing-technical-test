<?php


namespace Tests\Unit\Domain\Product;


use PHPUnit\Framework\TestCase;
use PrettyLittleThing\Product\Application\Command\CreateProductCommand;
use PrettyLittleThing\Product\Application\CommandHandler\CreateProductCommandHandler;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;

/**
 * @internal
 * @coversDefaultClass \PrettyLittleThing\Product\Application\CommandHandler\CreateProductCommandHandler
 */
class ProductCreateTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testAProductIsCreated()
    {
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $createProductCommand = $this->createMock(CreateProductCommand::class);

        $productRepository
            ->expects($this->once())
            ->method('create');

        $commandHandler = new CreateProductCommandHandler($productRepository);
        $commandHandler($createProductCommand);
    }
}