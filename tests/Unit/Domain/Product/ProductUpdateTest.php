<?php


namespace Tests\Unit\Domain\Product;


use PHPUnit\Framework\TestCase;
use PrettyLittleThing\Product\Application\Command\UpdateProductCommand;
use PrettyLittleThing\Product\Application\CommandHandler\UpdateProductCommandHandler;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;

/**
 * @internal
 * @coversDefaultClass \PrettyLittleThing\Product\Application\CommandHandler\UpdateProductCommandHandler
 */
class ProductUpdateTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testAProductIsUpdated()
    {
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $updateProductCommand = $this->createMock(UpdateProductCommand::class);

        $productRepository
            ->expects($this->once())
            ->method('update');

        $commandHandler = new UpdateProductCommandHandler($productRepository);
        $commandHandler($updateProductCommand);
    }
}