<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Symfony\Component\Messenger;

use Jamarcer\APM\Symfony\Component\Messenger\APMMiddleware;
use Jamarcer\Tests\APM\Mock\MessageMock;
use Jamarcer\Tests\APM\Mock\NameExtractorMock;
use Jamarcer\Tests\APM\Mock\ReporterMock;
use Jamarcer\Tests\APM\Mock\StackMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use ZoiloMora\ElasticAPM\Configuration\CoreConfiguration;
use ZoiloMora\ElasticAPM\ElasticApmTracer;
use ZoiloMora\ElasticAPM\Pool\Memory\MemoryPoolFactory;

class APMMiddlewareTest extends TestCase
{
    /** @test */
    public function when_message_is_dispatched_apm_registered_transaction(): void
    {
        $tracer = $this->getAPMTracer();
        $middleware = new APMMiddleware($tracer, NameExtractorMock::instance());

        $anEnvelop = new Envelope(new MessageMock('A message'));
        $aStack = new StackMock();

        $theEnvelope = $middleware->handle($anEnvelop, $aStack);
        self::assertEquals($anEnvelop, $theEnvelope);
        self::assertEquals(1, $middleware->countTransactions());
        self::assertTrue($middleware->transactionNameIs('A message'));
        self::assertTrue($middleware->transactionNamedIsOk('A message'));
    }

    private function getAPMTracer(): ElasticApmTracer
    {
        $configurator = CoreConfiguration::create(['appName' => 'Test',]);
        $reporter = new ReporterMock();
        $factory = MemoryPoolFactory::create();
        return new ElasticApmTracer($configurator, $reporter, $factory);
    }
}
