<?php

declare(strict_types=1);

use LoanFeeCalculator\Application\FeeCalculator;
use LoanFeeCalculator\Application\FeeCalculatorInterface;
use LoanFeeCalculator\Cli\CalculateFeeCommand;
use LoanFeeCalculator\Cli\FeeFormatter;
use LoanFeeCalculator\Domain\AmountParser;
use LoanFeeCalculator\Domain\Repository\JsonFeeStructureRepository;
use LoanFeeCalculator\Domain\Strategy\DivisibleByFiveRoundingStrategy;
use LoanFeeCalculator\Domain\Strategy\InterpolationStrategyInterface;
use LoanFeeCalculator\Domain\Strategy\LinearInterpolationStrategy;
use LoanFeeCalculator\Domain\Strategy\RoundingStrategyInterface;
use LoanFeeCalculator\Provider\FeeStructureProviderInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use function DI\autowire;
use function DI\get;

return [
    FeeStructureProviderInterface::class  => autowire(JsonFeeStructureRepository::class)
        ->constructorParameter('path', __DIR__ . '/fee_structure.json'),

    InterpolationStrategyInterface::class => autowire(LinearInterpolationStrategy::class),
    RoundingStrategyInterface::class      => autowire(DivisibleByFiveRoundingStrategy::class),

    FeeCalculator::class => autowire(FeeCalculator::class)
        ->constructorParameter('provider',      get(FeeStructureProviderInterface::class))
        ->constructorParameter('interpolation', get(InterpolationStrategyInterface::class))
        ->constructorParameter('rounding',      get(RoundingStrategyInterface::class)),

    FeeCalculatorInterface::class => get(FeeCalculator::class),

    AmountParser::class  => autowire(AmountParser::class),
    FeeFormatter::class  => autowire(FeeFormatter::class),

    LoggerInterface::class => \DI\factory(static function (): LoggerInterface {
        $logger = new Logger('loan-fee-calculator');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../var/app.log', Logger::DEBUG));
        return $logger;
    }),

    CalculateFeeCommand::class => autowire(CalculateFeeCommand::class)
        ->constructorParameter('calculator',   get(FeeCalculatorInterface::class))
        ->constructorParameter('amountParser', get(AmountParser::class))
        ->constructorParameter('feeFormatter', get(FeeFormatter::class))
        ->constructorParameter('logger',       get(LoggerInterface::class)),
];
