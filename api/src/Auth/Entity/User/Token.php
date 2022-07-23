<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class Token
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $value;
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value   = mb_strtolower($value);
        $this->expires = $expires;
    }

    public function validate(string $givenValue, DateTimeImmutable $givenDate): void
    {
        if (!$this->isEqualTo($givenValue)) {
            throw new DomainException('Token is not valid.');
        }

        if ($this->isExpiredTo($givenDate)) {
            throw new DomainException('Token was expired.');
        }
    }

    public function isExpiredTo(DateTimeImmutable $givenDate): bool
    {
        return $this->expires <= $givenDate;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    private function isEqualTo(string $givenValue): bool
    {
        return $this->value === $givenValue;
    }
}
