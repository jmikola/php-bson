<?php declare(strict_types=1);

namespace Jmikola\BSON;

final class PackedArray
{
    private readonly string $bson;

    private function __construct(string $bson)
    {
        $this->bson = $bson;
    }

    public static function fromBSON(string $bson): self
    {
        assert (strlen($bson) >= 5);
        assert (strlen($bson) === unpack('V', $bson)[1]);

        return new self($bson);
    }

    public static function fromPHP(array $value): self
    {
        assert(array_is_list($value));

        $writer = new BsonWriter();

        foreach ($value as $k => $v) {
            $writer->appendValue((string) $k, $v);
        }

        return new self($writer->getData());
    }

    public function toPHP(): array
    {
        
    }
}
