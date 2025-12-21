<?php

namespace App\Filters\Base;

enum FilterOperation: string
{
    case Like ='like';
    case EQ = 'eq';
    case NEQ = 'neq';
    case GT = 'gt';
    case GTE = 'gte';
    case LT = 'lt';
    case LTE = 'lte';
    case In = 'in';
    case NotIn = 'not-in';
    case Include = 'include';

}
