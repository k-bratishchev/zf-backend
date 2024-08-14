<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductsFilter extends ApiFilter {

    protected $safeParams = [
        'id' => self::ID_FILTERS,
        'name' => self::STRING_FILTERS,
        'price' => self::NUMBER_FILTERS,
        
    ];
}
