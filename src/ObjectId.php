<?php declare(strict_types=1);

namespace Jmikola\BSON;

final class ObjectId
{
    private const int COUNTER_MIN = 0;
    private const int COUNTER_MAX = 16_777_215;

    private static string $randomSequence;
    private static int $counter;

    public readonly string $bytes;

    private function __construct(string $bytes)
    {
        assert(strlen($bytes) === 12);

        $this->bytes = $bytes;
    }

    public static function fromBytes(string $bytes): self
    {
        return new self($bytes);
    }

    public static function fromCurrentTime(): self
    {
        $counter = self::getNextCounter();

        $bytes = pack(
            'Na5C3',
            time(),
            self::getRandomSequence(),
            $counter & 0xFF,
            ($counter >> 8) & 0xFF,
            ($counter >> 16) & 0xFF,
        );

        return new self($bytes);
    }

    public static function fromHexBytes(string $hexBytes): self
    {
        assert(strlen($hexBytes) === 24);
        assert(ctype_xdigit($hexBytes));

        return new self(hex2bin($hexBytes));
    }

    public function getTimestamp(): int
    {
        return unpack('N', $this->bytes)[1];
    }

    public function __toString(): string
    {
        return bin2hex($this->bytes);
    }

    private static function getRandomSequence(): string
    {
        // TODO: add fork detection to reinitialize this value
        if (!isset(self::$randomSequence)) {
            self::$randomSequence = random_bytes(5);
        }

        return self::$randomSequence;
    }

    private static function getNextCounter(): int
    {
        // TODO: add fork detection to reinitialize this value
        if (!isset(self::$counter)) {
            self::$counter = random_int(self::COUNTER_MIN, self::COUNTER_MAX);
        }

        if (++self::$counter > self::COUNTER_MAX) {
            self::$counter = 0;
        }

        return self::$counter;
    }
}
