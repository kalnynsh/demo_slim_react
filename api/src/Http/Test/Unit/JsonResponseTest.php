<?php

declare(strict_types=1);

namespace App\Http\Test\Unit;

use App\Http\Response\JsonResponse;
use JsonException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
final class JsonResponseTest extends TestCase
{
    public function testWithCode(): void
    {
        $response = new JsonResponse(0, 201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('0', $response->getBody()->getContents());
        self::assertEquals(201, $response->getStatusCode());
    }

    /**
     * @dataProvider getCases
     *
     * @throws JsonException
     */
    public function testResponse(mixed $source, mixed $expect): void
    {
        $response = new JsonResponse($source);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals($expect, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return iterable<array-key, array<array-key, mixed>>
     */
    public function getCases(): iterable
    {
        $object = new stdClass();
        $object->str = 'value';
        $object->int = 13;
        $object->none = null;

        $array = [
            'str' => 'value',
            'int' => 13,
            'none' => null,
        ];

        return [
            'null' => [null, 'null'],
            'empty' => ['', '""'],
            'number' => [13, '13'],
            'string' => ['13', '"13"'],
            'object' => [$object, '{"str":"value","int":13,"none":null}'],
            'array' => [$array, '{"str":"value","int":13,"none":null}'],
        ];
    }
}
