<?php
declare(strict_types=1);

namespace PrettyLittleThing\Product\Domain\Response;

class ProductCreatedResponse implements ResponseInterface
{
    private const ACTION = 'Created';

    public function getMessage(): string
    {
        return json_encode([
            'action' => ProductCreatedResponse::ACTION
        ]);
    }
}