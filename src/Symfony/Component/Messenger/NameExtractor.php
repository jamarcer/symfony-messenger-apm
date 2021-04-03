<?php

declare(strict_types=1);

namespace Jamarcer\APM\Symfony\Component\Messenger;

interface NameExtractor
{
    public function execute($message): string;
}