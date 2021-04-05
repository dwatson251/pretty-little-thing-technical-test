<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Infrastructure\Repository;

use Exception;

class ProductSkuNotFoundException extends Exception
{
    public function __construct(string $sku)
    {
        parent::__construct('Unable to find product with SKU of: `'.$sku.'`');
    }
}