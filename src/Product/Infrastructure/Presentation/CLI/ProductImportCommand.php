<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Infrastructure\Presentation\CLI;

use Generator;
use PrettyLittleThing\Product\Application\Command\ImportProductCommand;
use PrettyLittleThing\Product\Domain\Response\ProductCreatedResponse;
use PrettyLittleThing\Product\Domain\Response\ProductUpdatedResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Stopwatch\Stopwatch;

class ProductImportCommand extends Command
{
    protected static $defaultName = 'plt:product:import';

    private $requiredHeaderFormat = [
        'sku',
        'description',
        'normal_price',
        'special_price',
    ];

    private const DELIMITER = '|';

    /**
     * @var MessageBusInterface
     */
    private $eventBus;

    /**
     * @var Stopwatch
     */
    private $stopWatch;

    protected function configure()
    {
        $this
            ->setDescription('Given a CSV file to import from, imports a set of products')
            ->addOption(
                'filename',
                null,
                InputOption::VALUE_REQUIRED,
                'A filepath containing the CSV file you wish to use for importing products with.'
            )
            ->setHelp(
                "Given a CSV file to import from, imports a set of products. \n".
                "The fields in the CSV must be separated by a (|) and each row should not contain no more than 4 fields. \n\n".
                "The CSV is to contain the following header on the first row, and further rows conforming ".
                "the data that matches these: \n".
                "sku|description|normal_price|special_price \n\n".
                "sku* (string): The SKU for the product. Is used as the UUID and can only exist once. \n".
                "description* (string): A description of the product \n".
                "normal_price* (float): The normal price of this product \n".
                "special_price (float): Optional. The special price of this product. Must be lower than normal price. \n"
            );
        ;
    }

    public function __construct(
        MessageBusInterface $eventBus,
        Stopwatch $stopwatch
    ) {
        parent::__construct();
        $this->eventBus = $eventBus;
        $this->stopWatch = $stopwatch;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     *
     * @throws InputFileHasTooLittleFieldsException
     * @throws InputFileHasTooManyFieldsException
     * @throws InputFileHasInvalidHeaderException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->stopWatch->start('import');

        // Collect Input
        $file = $input->getOption('filename');

        $rowCount = 0;
        $errorCount = 0;

        $createdCount = 0;
        $updatedCount = 0;

        try {
            foreach ($this->parseInputFile($file) as $index => $row) {
                $rowCount++;

                try {
                    $envelope = $this->eventBus->dispatch(new ImportProductCommand(
                        $row['sku'],
                        $row['description'],
                        $row['normal_price'],
                        $row['special_price']
                    ));

                    /** @var HandledStamp $handled */
                    $handled = $envelope->last(HandledStamp::class);

                    switch (get_class($handled->getResult())) {
                        case ProductUpdatedResponse::class:
                            $updatedCount++;
                            break;
                        case ProductCreatedResponse::class:
                            $createdCount++;
                            break;
                        default:
                            throw new InvalidResponseException(
                                'The dispatcher returned an invalid response type.'
                            );
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            $stopwatchEvent = $this->stopWatch->stop('import');

            $output->writeln('Command Finished');
            $output->writeln(sprintf('Rows: %d', $rowCount));
            $output->writeln(sprintf('Errors: %d', $errorCount));
            $output->writeln(sprintf('Updates: %d', $updatedCount));
            $output->writeln(sprintf('Creates: %d', $createdCount));

            $output->writeln(sprintf('Memory: %s', $this->formatMemory($stopwatchEvent->getMemory())));
            $output->writeln(sprintf('Time: %s', $this->formatDuration($stopwatchEvent->getDuration())));

        } catch (\Exception $e) {
            $output->writeln("The input file could not be parsed: ".$e->getMessage());
        }

        return 0;
    }

    /**
     * @param string $file
     *
     * @return  Generator
     *
     * @throws InputFileHasInvalidHeaderException
     * @throws InputFileHasTooManyFieldsException
     * @throws InputFileHasTooLittleFieldsException
     * @throws InputFileIsEmptyException
     * @throws InputFileNotFoundException
     */
    private function parseInputFile(string $file): Generator
    {
        try {
            $handle = fopen($file, 'r');
        } catch (\Exception $e) {
            throw new InputFileNotFoundException('No such file found: '. $file);
        }

        $line = fgets($handle);

        if (false === $line) {
            throw new InputFileIsEmptyException('The file specified was empty.');
        }

        $headers = $this->rowToArray($line);

        if (false === $this->isFileHeaderValid($headers)) {
            throw new InputFileHasInvalidHeaderException(
                sprintf(
                    'The first row of the CSV must appear in the following manner: `%s`',
                    implode(ProductImportCommand::DELIMITER, $this->requiredHeaderFormat)
                )
            );
        }

        while (($line = fgets($handle)) !== false) {
            $rowFields = $this->rowToArray($line);

            unset($line);

            if (count($rowFields) > 4) {
                throw new InputFileHasTooManyFieldsException(
                    'The specified input file contains too many fields. Maximum is 4.'
                );
            }

            if (count($rowFields) < 3) {
                throw new InputFileHasTooLittleFieldsException(
                    'The specified file contains too little fields. Minimum is 3.'
                );
            }

            yield array_combine($headers, $rowFields);

            unset($rowFields);
        }

        unset($headers);
    }

    private function isFileHeaderValid(array $headers): bool
    {
        return $headers === $this->requiredHeaderFormat;
    }

    private function rowToArray(string $row)
    {
        return array_map(function(string $field) {
            return trim($field);
        }, explode(ProductImportCommand::DELIMITER, $row));
    }

    /**
     * @param int $bytes Memory in bytes
     * @return string
     */
    private function formatMemory($bytes)
    {
        return round($bytes / 1000 / 1000, 2) . ' MB';
    }

    /**
     * @param int $microseconds Time in microseconds
     * @return string
     */
    private function formatDuration($microseconds)
    {
        return $microseconds / 1000 . ' s';
    }
}