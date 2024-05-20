<?php declare(strict_types=1);

namespace Jmikola\BSON;

enum BsonType: int
{
    case Double = 1;
    case String = 2;
    case Document = 3;
    case Array = 4;
    case Binary = 5;
    case Undefined = 6;
    case ObjectId = 7;
    case Bool = 8;
    case UTCDateTime = 9;
    case Null = 10;
    case Rregex = 11;
    case DBPointer = 12;
    case Code = 13;
    case Symbol = 14;
    case CodeWithScope = 15;
    case Int32 = 16;
    case Timestamp = 17;
    case Int64 = 18;
    case Decimal128 = 19;
    case MinKey = -1;
    case MaxKey = 127;
}
