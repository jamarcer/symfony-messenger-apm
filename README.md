# Symfony Messenger APM Middleware

This library supports Span traces of [Symfony Messenger](https://github.com/symfony/messenger) messages.
This library is based on [Elastic APM for Symfony Messenger](https://github.com/PcComponentes/apm-symfony-messenger).

## Installation

Install via [composer](https://getcomposer.org/)

```shell script
composer require jamarcer/symfony_messenger_apm
```

## Usage

It is necessary to have a previously created [ElasticApmTracer](https://github.com/zoilomora/elastic-apm-agent-php) instance.

```shell script
apm.tracer:
    class: ZoiloMora\ElasticAPM\ElasticApmTracer
    factory: ['App\Service\ApmService', 'instantiate']
    arguments: ['apm-desa','http://localhost:7200','desa']
```

And a NameExtractor implementation.

```shell script
 app.bus.middleware.apm.name_extractor:
        class: CNA\Infrastructure\Symfony\Component\Messenger\MessageNameExtractor
```

Then you declare the middleware.

```shell script

messenger.bus.middleware.apm:
    class: Jamarcer\APM\Symfony\Component\Messenger\ApmMiddleware
    arguments:
        $elasticApmTracer: '@apm.tracer'
        $nameExtractor: '@app.bus.middleware.apm.name_extractor'
```

## Development

Prepare the development environment. 

```shell script
make build
```

```shell script
make composer-install
```

Or you can access directly to bash ...

```shell script
make start
```

... and test the library

```shell script
/var/app/vendor/bin/phpunit  --configuration /var/app/phpunit.xml.dist 
```

## License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).

Read [LICENSE](LICENSE) for more information
