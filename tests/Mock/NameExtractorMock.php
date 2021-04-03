<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Mock;

use Jamarcer\APM\Symfony\Component\Messenger\NameExtractor;

final class NameExtractorMock implements NameExtractor
{
    public function execute($message): string
    {
        return $message->getContent();
    }

    public static function instance(): NameExtractorMock
    {
        return new NameExtractorMock();
    }
}