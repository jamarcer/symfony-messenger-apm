<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Mock;

use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class StackMock implements StackInterface
{
    public function next(): MiddlewareInterface
    {
        return new MiddlewareMock();
    }
}