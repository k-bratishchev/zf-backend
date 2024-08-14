<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrdersFilter extends ApiFilter {

    protected $safeParams = [
        'id' => self::ID_FILTERS,
        'user_id' => self::ID_FILTERS,
    ];
}
