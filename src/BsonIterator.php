<?php declare(strict_types=1);

namespace Jmikola\BSON;

use Iterator;

class BsonIterator implements Iterator
{
    private readonly string $bson;
    private readonly int $bsonSize;
    private int $offset;
    private int $keyOffset;
    private int $
    private int $nextOffset;

    private mixed $current;
    private string $key;
    private bool $valid;

    public function __construct(string $bson)
    {
        $bsonSize = strlen($bson);

        assert($bsonSize >= 5);
        assert($bsonSize === unpack('V', $bson)[1]);

        $this->bson = $bson;
        $this->bsonSize = $bsonSize;
        $this->offset = 0;
        $this->nextOffset = 4;
    }

    public function current(): mixed
    {
        assert($this->bson[$this->offset] !== 0);
    }

    public function key(): string
    {
        assert($this->bson[$this->offset] !== 0);
        assert($this->valid);

        return unpack('Z*', $this->bson, $this->offset + 1);
    }

    public function next(): void
    {
        $this->consumeNextElement();
    }

    public function rewind(): void
    {
        $this->offset = 4;

        if ($this->bson[$this->offset] !== 0) {
            $this->consumeNextElement();
        }
    }
    
    public function valid(): bool
    {
        return $this->valid;
    }

    private function consumeNextElement(): bool
    {
        $t
        if ($this->bson[$this->offset] === 0) {
            return false;
        }

        assert($this->offset + 2 < $this->bsonSize); 

        [,$type, $key] = unpack('cZ*', $this->bson, $this->offset);
        $this->offset += 1 + strlen($key) + 1;

        switch (BsonType::tryFrom($type)) {
            case BsonType::Array:
                assert($this->offset + 5 < $this->bsonSize);
                $len = unpack('V', $this->bson, $this->offset)[1];
                $value = PackedArray::fromBSON(substr($this->bson, $this->offset, $len));
                $this->offset += $len;
                break;

            case BsonType::Document:
                assert($this->offset + 5 < $this->bsonSize);
                $len = unpack('V', $this->bson, $this->offset)[1];
                $value = Document::fromBSON(substr($this->bson, $this->offset, $len));
                $this->offset += $len;
                break;

            case BsonType::String:
                assert($this->offset + 5 < $this->bsonSize);
                $len = unpack('V', $this->bson, $this->offset)[1];
                $this->offset += 4;
                $value = substr($this->bson, $this->offset, $len - 1);
                $this->offset += $len;
                break;

            case BsonType::Int32:
                assert($this->offset + 4 < $this->bsonSize);
                $value = unpack('V', $this->bson, $this->offset)[1];
                $this->offset += 4;
                break;

            case BsonType::Int64:
                assert($this->offset + 8 < $this->bsonSize);
                $value = unpack('P', $this->bson, $this->offset)[1];
                $this->offset += 8;
                break;

            case BsonType::Bool:
                assert($this->offset + 1 < $this->bsonSize);
                // Note: this accepts any non-zero value as true
                $value = unpack('C', $this->bson, $this->offset)[1] !== 0;
                $this->offset += 1;

            case BsonType::Double:
                assert($this->offset + 8 < $this->bsonSize);
                $value = unpack('e', $this->bson, $this->offset)[1];
                $this->offset += 8;
                break;

            case BsonType::ObjectId:
                assert($this->offset + 12 < $this->bsonSize);
                $value = ObjectId::fromBytes(unpack('a12', $this->bson, $this->offset)[1]);
                $this->offset += 12;
                break;

            default:
                throw new RuntimeException('Unsupported type: ' . $type);
        }

        $this->key = $key;
        $this->value = $value;
    }
}
