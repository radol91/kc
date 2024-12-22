<?php

declare(strict_types=1);


namespace App\Tests\Application;

use App\Application\GetProvidersQuotesUseCase;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetProvidersQuotesUseCaseTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->getContainer()->get(ValidatorInterface::class);
    }

    public function testItConstructsEmptyUseCase(): void
    {
        $useCase = new GetProvidersQuotesUseCase([]);
        $violations = $this->validator->validate($useCase);

        self::assertCount(0, $violations, 'Use case should be valid.');
        self::assertEmpty($useCase->topics);
    }

    public function testItConstructsValidUseCase(): void
    {
        $useCase = new GetProvidersQuotesUseCase($topics = [
            'topic1' => 1,
            'topic2' => 2,
        ]);
        $violations = $this->validator->validate($useCase);
        self::assertCount(0, $violations, 'Use case should be valid.');
        self::assertEquals($topics, $useCase->topics);
    }

    public function testItValidatesValuesAreIntegers(): void
    {
        $useCase = new GetProvidersQuotesUseCase([
            'topic1' => 10,
            'topic2' => 'not-int',
        ]);
        $violations = $this->validator->validate($useCase);
        self::assertCount(1, $violations, 'Use case should be invalid.');
    }

    public function testItValidatesValuesAreGreaterThan0(): void
    {
        $useCase = new GetProvidersQuotesUseCase([
            'topic1' => -1,
            'topic2' => 100,
        ]);
        $violations = $this->validator->validate($useCase);
        self::assertCount(1, $violations, 'Use case should be invalid.');

        $useCase = new GetProvidersQuotesUseCase([
            'topic1' => 10,
            'topic2' => -1,
        ]);
        $violations = $this->validator->validate($useCase);
        self::assertCount(1, $violations, 'Use case should be invalid.');
    }

    /** @dataProvider totalAndTopicsProvider */
    public function testItGetsTotalByFilteredTopics(
        array $topics,
        array $filtered,
        int $expectedTotal
    ): void {
        $useCase = new GetProvidersQuotesUseCase($topics);

        self::assertEquals($expectedTotal, $useCase->getTotalByTopics($filtered));
    }

    public static function totalAndTopicsProvider(): \Generator
    {
        $topics = [
            'topic1' => 1,
            'topic2' => 10,
            'topic3' => 20,
        ];

        yield 'all topics' => [$topics, array_keys($topics), 31];
        yield 'filtered topics #1' => [$topics, ['topic2', 'topic3'], 30];
        yield 'filtered topics #2' => [$topics, ['topic1', 'topic3'], 21];
        yield 'single topic' => [$topics, ['topic2'], 10];
        yield 'not-existing topic' => [$topics, ['not-exists'], 0];
        yield 'empty filter' => [$topics, [], 0];
    }
}
