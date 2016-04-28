<?php

namespace Mattbit\Flat\Query\Expression;

class Type
{
    const EQ = '$eq';
    const GT = '$gt';
    const GTE = '$gte';
    const LT = '$lt';
    const LTE = '$lte';
    const NE = '$ne';
    const NIN = '$nin';
    const IN = '$in';
    const REGEX = '$regex';

    const AND_MATCH = '$and';
    const OR_MATCH = '$or';
    const NOR = '$nor';
    const NOT = '$not';

    const ELEM_MATCH = '$elemMatch';
}
