<?php declare(strict_types=1);

namespace Jmikola\BSON;

final class Document
{
    private readonly string $bson;

    private function __construct(string $bson)
    {
        assert (strlen($bson) >= 5);
        assert (strlen($bson) === unpack('V', $bson)[1]);

        $this->bson = $bson;
    }

    public static function fromBSON(string $bson): self
    {
        return new self($bson);
    }

    public static function fromPHP(object|array $value): self
    {
        if ($value instanceof Document) {
            return clone $value;
        }

        if (is_object($value)) {
            $value = get_object_vars($value);
        }

        $writer = new BsonWriter();

        foreach ($value as $k => $v) {
            $writer->appendValue($k, $v);
        }

        return new self($writer->getData());
    }
}
