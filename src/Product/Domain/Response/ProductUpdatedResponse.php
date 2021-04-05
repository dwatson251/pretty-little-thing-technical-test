<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Domain\Response;

class ProductUpdatedResponse implements ResponseInterface
{
    private const ACTION = 'Updated';

    public function getMessage(): string
    {
        return json_encode([
            'action' => ProductUpdatedResponse::ACTION
        ]);
    }
}