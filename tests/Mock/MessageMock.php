<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Mock;

final class MessageMock
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}