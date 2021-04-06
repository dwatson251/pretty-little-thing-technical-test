<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Domain\Handler;


use PrettyLittleThing\Product\Application\Command\CreateProductCommand;
use PrettyLittleThing\Product\Application\Command\ImportProductCommand;
use PrettyLittleThing\Product\Application\Command\UpdateProductCommand;
use PrettyLittleThing\Product\Domain\Model\NormalPriceBelowZeroException;
use PrettyLittleThing\Product\Domain\Model\NormalPriceNotFloatException;
use PrettyLittleThing\Product\Domain\Model\ProductRepositoryInterface;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceAboveNormalPriceException;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceBelowZeroException;
use PrettyLittleThing\Product\Domain\Model\SpecialPriceNotFloatException;
use PrettyLittleThing\Product\Infrastructure\Repository\ProductSkuNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class ImportProductCommandHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private $eventBus;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        MessageBusInterface $eventBus,
        ProductRepositoryInterface $productRepository
    ) {
        $this->eventBus = $eventBus;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ImportProductCommand $command
     *
     * @throws NormalPriceNotFloatException
     * @throws SpecialPriceAboveNormalPriceException
     * @throws SpecialPriceNotFloatException
     * @throws SpecialPriceBelowZeroException
     * @throws NormalPriceBelowZeroException
     */
    public function __invoke(ImportProductCommand $command)
    {
        $normalPrice = null;
        $specialPrice = null;

        // Validation
        if (false === $this->isFloat($command->getNormalPrice())) {
            throw new NormalPriceNotFloatException(
                sprintf('The normalPrice on sku %s provided must be a positive float', $command->getSku())
            );
        }

        // Convert normalPrice to pence
        $normalPrice = (int) $command->getNormalPrice() * 100;

        if ($normalPrice < 0) {
            throw new NormalPriceBelowZeroException(
                sprintf('The normalPrice on sku %s provided must be a positive float', $command->getSku())
            );
        }

        if (false === empty($command->getSpecialPrice())) {
            if (false === $this->isFloat($command->getSpecialPrice())) {
                throw new SpecialPriceNotFloatException(
                    sprintf('The specialPrice on sku %s provided must be a positive float', $command->getSku())
                );
            }

            if ($command->getNormalPrice() < $command->getSpecialPrice()) {
                throw new SpecialPriceAboveNormalPriceException(
                    sprintf('The specialPrice on sku %s is higher than the normalPrice', $command->getSku())
                );
            }

            // Convert specialPrice to pence
            $specialPrice = (int) $command->getSpecialPrice() * 100;

            if ($specialPrice < 0) {
                throw new SpecialPriceBelowZeroException(
                    sprintf('The specialPrice on sku %s provided must be a positive float', $command->getSku())
                );
            }
        }

        try {
            $product = $this->productRepository->findBySku($command->getSku());

            $envelope = $this->eventBus->dispatch(new UpdateProductCommand(
                $product->getSku(),
                $command->getDescription(),
                $normalPrice,
                $specialPrice
            ));

        } catch (ProductSkuNotFoundException $e) {
            $envelope = $this->eventBus->dispatch(new CreateProductCommand(
                $command->getSku(),
                $command->getDescription(),
                $normalPrice,
                $specialPrice
            ));
        }

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);
        return $handled->getResult();
    }

    private function isFloat(string $value): bool
    {
        return ($value == (string)(float) $value);
    }
}


