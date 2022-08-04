<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final class UserBuilder
{
    private Id $id;
    private DateTimeImmutable $date;
    private Email $email;
    private string $passwordHash;
    private ?Token $joinConfirmToken;
    private bool $active = false;
    private ?Network $networkIdentity = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->date = new DateTimeImmutable();
        $this->email = new Email('john_dough@info.org');

        $this->passwordHash = 'hash';
        $this->joinConfirmToken = new Token(
            Uuid::uuid4()->toString(),
            $this->date->modify('+1 day')
        );
    }

    public function viaNetwork(Network $network = null): self
    {
        $clone = clone $this;
        $clone->networkIdentity = $network ?? new Network('instagram', '100003');

        return $clone;
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;

        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;

        return $clone;
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function withPasswordHash(?string $passwordHash = null): self
    {
        $clone = clone $this;

        if ($passwordHash !== null) {
            $clone->passwordHash = $passwordHash;
        }

        if ($passwordHash === null) {
            $clone->passwordHash = $this->getHasher()->hash('very-secret-295');
        }

        return $clone;
    }

    public function build(): User
    {
        if ($this->networkIdentity !== null) {
            return User::joinByNetwork(
                $this->id,
                $this->date,
                $this->email,
                $this->networkIdentity
            );
        }

        /** @psalm-suppress PossiblyNullArgument */
        $user = User::requestJoinByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->passwordHash,
            $this->joinConfirmToken
        );

        if ($this->active) {
            $user
                ->confirmJoin(
                    $this->joinConfirmToken->getValue(),
                    $this->joinConfirmToken->getExpires()->modify('-1 day')
                );
        }

        return $user;
    }

    private function getHasher(): PasswordHasher
    {
        return new PasswordHasher(16);
    }
}
