<?php

namespace App\Infrastructure\Parser;

enum ProductField: string
{
    case EXTERNAL_CODE = 'Внешний код';
    case NAME = 'Наименование';
    case DESCRIPTION = 'Описание';
    case SELLING_PRICE = 'Цена: Цена продажи';
    case COST_PRICE = 'Закупочная цена';
}