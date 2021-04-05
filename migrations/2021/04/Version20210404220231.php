<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210404220231 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates the products table';
    }

    public function up(Schema $schema) : void
    {
        $productsTable = $schema->createTable('products');

        $productsTable->addColumn('sku', 'string', [
            'notnull' => true
        ]);

        $productsTable->addColumn('description', 'string', [
            'notnull' => true
        ]);

        $productsTable->addColumn('normal_price', 'integer', [
            'notnull' => true,
            'unsigned' => true,
        ]);

        $productsTable->addColumn('special_price', 'integer', [
            'notnull' => false,
            'unsigned' => true,
        ]);

        $productsTable->addUniqueIndex(['sku'], 'sku_u_idx');
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('products');
    }
}
