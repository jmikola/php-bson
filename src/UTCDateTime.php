<?php declare(strict_types=1);

namespace Jmikola\BSON;

use DateTime;
use DateTimeInterface;

final class UTCDateTime
{
    private readonly int $milliseconds;

    public function __construct(int|DateTimeInterface|null $milliseconds = null)
    {
        if (is_int($milliseconds)) {
            $this->milliseconds = $milliseconds;
            return;
        }

        if ($milliseconds instanceof DateTimeInterface) {
            $this->milliseconds = $milliseconds->getTimestamp() * 1000 + ((int) $milliseconds->format('v'));
            return;
        }

        $this->milliseconds = (int) (microtime(true) * 1000);
    }

    public function toDateTime(): DateTime {}

    final public function __toString(): string {}
}