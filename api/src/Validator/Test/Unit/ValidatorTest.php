<?php

declare(strict_types=1);

namespace App\Validator\Test\Unit;

use App\Validator\ValidationException;
use App\Validator\Validator;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \App\Validator\Validator
 *
 * @internal
 */
final class ValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $command = new stdClass();
        $origin = $this->createMock(ValidatorInterface::class);

        $origin
            ->expects(self::once())
            ->method('validate')
            ->with(self::equalTo($command))
            ->willReturn(new ConstraintViolationList());

        $customValidator = new Validator($origin);
        $customValidator->validate($command);
    }

    public function testNotValid(): void
    {
        $command = new stdClass();
        $origin = $this->createMock(ValidatorInterface::class);

        $origin
            ->expects(self::once())
            ->method('validate')
            ->with(self::equalTo($command))
            ->willReturn($violations = new ConstraintViolationList([
                $this->createMock(ConstraintViolation::class),
            ]));

        $customValidator = new Validator($origin);

        try {
            $customValidator->validate($command);
            self::fail('Expected exception is not thrown');
        } catch (Exception $exception) {
            self::assertInstanceOf(ValidationException::class, $exception);

            self::assertEquals($violations, $exception->getViolations());
        }
    }
}
