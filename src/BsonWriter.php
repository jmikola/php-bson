<?php declare(strict_types=1);

namespace Jmikola\BSON;

class BsonWriter
{
    private const int INT32_MAX = 2_147_483_647;
    private const int INT32_MIN = -2_147_483_648;

    private array $elements = [];

    public function appendArray(string $key, PackedArray $value): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*a*', BsonType::Array->value, $key, $value->getData());
    }

    public function appendBinary(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendBool(string $key, bool $value): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*C', BsonType::Bool->value, $key, $value ? 1 : 0);
    }

    public function appendCode(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendCodeWithScope(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendDBPointer(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendDecimal128(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendDocument(string $key, Document $value): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*a*', BsonType::Document->value, $key, $value->getData());
    }

    public function appendDouble(string $key, float $value): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*e', BsonType::Double->value, $key, $value);
    }

    private function appendInt32(string $key, int $value): void
    {
        assert(! str_contains($key, "\0"));
        assert($value <= self::INT32_MAX && $value >= self::INT32_MIN);
        $this->elements[] = pack('cZ*V', BsonType::Int32->value, $key, $value);
    }

    private function appendInt64(string $key, int $value): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*P', BsonType::Int64->value, $key, $value);
    }

    public function appendMaxKey(string $key): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*', BsonType::MaxKey->value, $key);
    }

    public function appendMinKey(string $key): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*', BsonType::MinKey->value, $key);
    }

    public function appendNull(string $key): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*', BsonType::Null->value, $key);
    }

    public function appendObjectId(string $key, ObjectId $value): void
    {
        assert(! str_contains($key, "\0"));
        assert(strlen($value->bytes) === 12);
        $this->elements[] = pack('cZ*a12', BsonType::ObjectId->value, $key, $value->bytes);
    }

    public function appendRegex(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendString(string $key, string $value): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*VZ*', BsonType::String->value, $key, strlen($value) + 1, $value);
    }

    public function appendSymbol(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendTimestamp(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendUndefined(string $key): void
    {
        assert(! str_contains($key, "\0"));
        $this->elements[] = pack('cZ*', BsonType::Undefined->value, $key);
    }

    public function appendUTCDateTime(string $key): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function appendValue(string $key, mixed $value): void
    {
        if (is_object($value)) {
            if ($value instanceof ObjectId) {
                $this->appendObjectId($key, $value);
            } else {
                $this->appendDocument($key, Document::fromPHP($value));
            }
        } elseif (is_array($value)) {
            if (array_is_list($value)) {
                $this->appendArray($key, PackedArray::fromPHP($value));
            } else {
                $this->appendDocument($key, Document::fromPHP($value));
            }
        } elseif (is_string($value)) {
            $this->appendString($key, $value);
        } elseif (is_int($value)) {
            if ($value > self::INT32_MAX || $value < self::INT32_MIN) {
                $this->appendInt64($key, $value);
            } else {
                $this->appendInt32($key, $value);
            }
        } elseif (is_bool($value)) {
            $this->appendBool($key, $value);
        } elseif (is_float($value)) {
            $this->appendDouble($key, $value);
        } else {
            throw new RuntimeException('Unsupported type: ' . get_debug_type($value));
        }
    }

    public function getData(): string
    {
        $bson = implode($this->elements);

        return pack('Va*x', strlen($bson) + 5, $bson);
    }
}
