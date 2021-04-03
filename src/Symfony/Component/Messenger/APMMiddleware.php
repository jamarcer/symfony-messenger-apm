<?php

declare(strict_types=1);

namespace Jamarcer\APM\Symfony\Component\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;
use ZoiloMora\ElasticAPM\ElasticApmTracer;

final class APMMiddleware implements MiddlewareInterface
{
    private const STACKTRACE_SKIP = 4;

    private ElasticApmTracer $elasticApmTracer;
    private NameExtractor $nameExtractor;

    private array $trace;

    public function __construct(
        ElasticApmTracer $elasticApmTracer,
        NameExtractor $nameExtractor
    ) {
        $this->elasticApmTracer = $elasticApmTracer;
        $this->nameExtractor = $nameExtractor;
        $this->trace = [];
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (false === $this->elasticApmTracer->active()) {
            return $stack->next()->handle($envelope, $stack);
        }

        $name = $this->nameExtractor->execute(
            $envelope->getMessage()
        );

        $span = null;

        try {
            $span = $this->elasticApmTracer->startSpan(
                $name,
                'message',
                null,
                null,
                null,
                self::STACKTRACE_SKIP
            );
        } catch (Throwable $exception) {
            //nothing
        }

        $transaction = $this->elasticApmTracer->startTransaction(
            $name,
            'message'
        );
        $this->trace[] = $transaction;

        try {
            $envelope = $stack->next()->handle($envelope, $stack);

            $transaction->stop('OK');
            if (null !== $span) {
                $span->stop();
            }
        } catch (Throwable $throwable) {
            $this->elasticApmTracer->captureException($throwable);

            $transaction->stop('KO');
            if (null !== $span) {
                $span->stop();
            }

            throw $throwable;
        }

        return $envelope;
    }

    public function countTransactions(): int
    {
        return count($this->trace);
    }

    public function transactionNameIs(string $name): bool
    {
        foreach ($this->trace as $transaction) {
            $tr = $transaction->jsonSerialize();
            if ($name === $transaction->jsonSerialize()['transaction']['name']) {
                return true;
            }
        }
        return false;
    }

    public function transactionNamedIsOk(string $name): bool
    {
        foreach ($this->trace as $transaction) {
            $trData = $transaction->jsonSerialize()['transaction'];
            if ($name === $trData['name']) {
                return 'OK' === $trData['result'];
            }
        }
        return false;
    }
}