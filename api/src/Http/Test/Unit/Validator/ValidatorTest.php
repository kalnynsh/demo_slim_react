<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Validator;

use stdClass;
use PHPUnit\Framework\TestCase;
use App\Http\Validator\Validator;
use App\Http\Validator\ValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \App\Http\Validator\Validator
 */
class ValidatorTest extends TestCase
{
    public function testValid(): void
    {
        $command = new stdClass();
        $origin = $this->createMock(ValidatorInterface::class);

        $origin
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($command))
            ->willReturn(new ConstraintViolationList());

        $customValidator = new Validator($origin);
        $customValidator->validate($command);
    }

    public function testNotValid(): void
    {
        $command = new stdClass();
        $origin = $this->createMock(ValidatorInterface::class);

        $origin
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($command))
            ->willReturn($violations = new ConstraintViolationList([
                $this->createMock(ConstraintViolation::class),
            ]));

        $customValidator = new Validator($origin);

        try {
            $customValidator->validate($command);
            self::fail('Expected exception is not thrown');
        } catch (\Exception $exception) {
            self::assertInstanceOf(ValidationException::class, $exception);
            /** @var ConstraintViolationList $violations */
            self::assertEquals($violations, $exception->getViolations());
        }
    }
}
