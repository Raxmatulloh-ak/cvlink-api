<?php

namespace App\Enum;

enum AttributeValueType:string
{
    case STRING = 'string';
    case TEXT = 'text';
    case IMAGE = 'image';
    case NUMERIC = 'numeric';
    case DATE = 'date';
    case PERIOD = 'period';
    case BOOLEAN = 'boolean';
    case DROPDOWN = 'dropdown';
}
