<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Auth\Cycle;

use Cycle\Annotated\Annotation as Cycle;
use Spiral\Auth\TokenInterface;

/**
 * @Cycle\Entity(table="auth_tokens")
 */
final class Token implements TokenInterface, \JsonSerializable
{
    /** @Cycle\Column(type="string(64)", primary=true) */
    private $id;

    /** @var string */
    private $secretValue;

    /** @Cycle\Column(type="string(128)", name="hashed_value") */
    private $hashedValue;

    /** @Cycle\Column(type="datetime") */
    private $createdAt;

    /** @Cycle\Column(type="datetime", nullable=true) */
    private $expiresAt;

    /** @Cycle\Column(type="blob") */
    private $payload;

    /**
     * @param string                  $id
     * @param string                  $secretValue
     * @param array                   $payload
     * @param \DateTimeImmutable      $createdAt
     * @param \DateTimeInterface|null $expiresAt
     */
    public function __construct(
        string $id,
        string $secretValue,
        array $payload,
        \DateTimeImmutable $createdAt,
        \DateTimeInterface $expiresAt = null
    ) {
        $this->id = $id;

        $this->secretValue = $secretValue;
        $this->hashedValue = hash('sha512', $secretValue);

        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;

        $this->payload = json_encode($payload);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getID();
    }

    /**
     * @param string $value
     */
    public function setSecretValue(string $value): void
    {
        $this->secretValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function getID(): string
    {
        return sprintf('%s:%s', $this->id, $this->secretValue);
    }

    /**
     * @return string
     */
    public function getHashedValue(): string
    {
        return $this->hashedValue;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        return json_decode($this->payload, true);
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->getID();
    }
}
