<?php


namespace Tests\Unit\Domain\Product;


use PHPUnit\Framework\TestCase;
use PrettyLittleThing\Product\Application\Command\CreateProductCommand;
use PrettyLittleThing\Product\Application\Command\ImportProductCommand;
use PrettyLittleThing\Product\Application\Command\UpdateProductCommand;
use PrettyLittleThing\Product\Domain\Handler\ImportProductCommandHandler;
use PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException;
use PrettyLittleThing\Product\Domain\Model\NormalPriceNotFloatException;
use PrettyLittleThing\Product\Domain\Model\Product;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException;
use PrettyLittleThing\Product\Domain\Response\ProductCreatedResponse;
use PrettyLittleThing\Product\Domain\Response\ProductUpdatedResponse;
use PrettyLittleThing\Product\Infrastructure\Repository\ProductSkuNotFoundException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 * @coversDefaultClass \PrettyLittleThing\Product\Domain\Handler\ImportProductCommandHandler
 */
class ProductImportTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MessageBusInterface
     */
    private $eventBus;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProductRepositoryInterface
     */
    private $productRepository;

    public function setUp(): void
    {
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceNotFloatException
     * @throws \PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException
     */
    public function testExceptionThrownWhenNormalPriceIsNotAFloat()
    {
        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->once())
            ->method('getNormalPrice')
            ->willReturn('NotAFloat');

        $this->expectException(NormalPriceNotFloatException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceNotFloatException
     * @throws \PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException
     */
    public function testExceptionThrownWhenNormalPriceIsNegativeValue()
    {
        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->exactly(2))
            ->method('getNormalPrice')
            ->willReturn('-1.99');

        $this->expectException(NormalPriceBelowZeroException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceNotFloatException
     * @throws \PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException
     */
    public function testExceptionThrownWhenSpecialPriceIsNotAFloat()
    {
        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->exactly(2))
            ->method('getNormalPrice')
            ->willReturn('69.99');

        $command
            ->expects($this->exactly(2))
            ->method('getSpecialPrice')
            ->willReturn('NotAFloat');

        $this->expectException(SpecialPriceNotFloatException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceNotFloatException
     * @throws \PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException
     */
    public function testExceptionThrownWhenSpecialPriceMoreThanNormalPrice()
    {
        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->exactly(3))
            ->method('getNormalPrice')
            ->willReturn('69.99');

        $command
            ->expects($this->exactly(3))
            ->method('getSpecialPrice')
            ->willReturn('109.99');

        $this->expectException(SpecialPriceAboveNormalPriceException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceNotFloatException
     * @throws \PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException
     * @throws \PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException
     */
    public function testExceptionThrownWhenSpecialPriceIsNegativeValue()
    {
        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->exactly(3))
            ->method('getNormalPrice')
            ->willReturn('69.99');

        $command
            ->expects($this->exactly(4))
            ->method('getSpecialPrice')
            ->willReturn('-109.99');

        $this->expectException(SpecialPriceBelowZeroException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceBelowZeroException
     * @throws NormalPriceNotFloatException
     * @throws SpecialPriceAboveNormalPriceException
     * @throws SpecialPriceBelowZeroException
     * @throws SpecialPriceNotFloatException
     */
    public function testUpdateProductCommandDispatchedWhenProductFound()
    {
        $this->markTestIncomplete('Messenger Envelope cannot be mocked. Work-around required.');

        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->exactly(3))
            ->method('getNormalPrice')
            ->willReturn('109.99');

        $command
            ->expects($this->exactly(4))
            ->method('getSpecialPrice')
            ->willReturn('69.99');

        $command
            ->expects($this->once())
            ->method('getSku')
            ->willReturn('deadbeef');

        $product = $this->createMock(Product::class);

        $this->productRepository
            ->expects($this->once())
            ->method('findBySku')
            ->willReturn($product);

        $product
            ->expects($this->once())
            ->method('getSku')
            ->willReturn('deadbeef');

        $command
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Test Product');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new UpdateProductCommand('deadbeef', 'Test Product', 109.99, 69.99)
            )
            ->willReturn(new Envelope(new ProductUpdatedResponse()));

        $this->expectException(SpecialPriceBelowZeroException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }

    /**
     * @covers ::__invoke
     *
     * @throws NormalPriceBelowZeroException
     * @throws NormalPriceNotFloatException
     * @throws SpecialPriceAboveNormalPriceException
     * @throws SpecialPriceBelowZeroException
     * @throws SpecialPriceNotFloatException
     */
    public function testCreateProductCommandDispatchedWhenProductNotFound()
    {
        $this->markTestIncomplete('Messenger Envelope cannot be mocked. Work-around required.');

        $command = $this->createMock(ImportProductCommand::class);

        $command
            ->expects($this->exactly(3))
            ->method('getNormalPrice')
            ->willReturn('109.99');

        $command
            ->expects($this->exactly(4))
            ->method('getSpecialPrice')
            ->willReturn('69.99');

        $command
            ->expects($this->once())
            ->method('getSku')
            ->willReturn('deadbeef');

        $this->productRepository
            ->expects($this->once())
            ->method('findBySku')
            ->willThrowException(new ProductSkuNotFoundException('deadbeef'));

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new CreateProductCommand('deadbeef', 'Test Product', 109.99, 69.99)
            )
            ->willReturn(new Envelope(new ProductCreatedResponse()));

        $this->expectException(SpecialPriceBelowZeroException::class);

        $commandHandler = new ImportProductCommandHandler($this->eventBus, $this->productRepository);
        $commandHandler($command);
    }
}