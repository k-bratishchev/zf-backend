<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApiFilter {

    const ALL_FILTERS = ['eq', 'lt', 'lte', 'gt', 'gte', 'like', 'ne', 'in', 'is', 'is_not'];
    const NUMBER_FILTERS = ['eq', 'lt', 'lte', 'gt', 'gte', 'ne', 'in', 'is', 'is_not'];
    const STRING_FILTERS = ['eq', 'ne', 'in', 'not_in', 'like', 'is', 'is_not'];
    const DATE_FILTERS = ['eq', 'lt', 'lte', 'gt', 'gte', 'ne', 'is', 'is_not'];
    const ID_FILTERS = ['eq', 'lt', 'lte', 'gt', 'gte', 'ne', 'in', 'not_in'];

    protected $safeParams = [];
    protected $columnMap = [];
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!=',
        'like' => 'like',
        'in' => 'in',
        'is' => 'is',
        'is_not' => 'is not',
    ];

    public function transformQuery(Builder $query, Request $request) {
        foreach ($this->safeParams as $param => $operators) {
            $requestParam = $request->query($param);

            if (!isset($requestParam)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($requestParam[$operator])) {
                    switch ($this->operatorMap[$operator]) {
                        case 'like':
                            $query->where($column, 'like', "%$requestParam[$operator]%");
                            break;
                        case 'in':
                            $query->whereIn($column, explode(',', $requestParam[$operator]));
                            break;
                        case 'not_in':
                            $query->whereNotIn($column, explode(',', $requestParam[$operator]));
                            break;
                        case 'is':
                            if ($requestParam[$operator] === 'null') {
                                $query->whereNull($column);
                            }
                            break;
                        case 'is not':
                            if ($requestParam[$operator] === 'null') {
                                $query->whereNotNull($column);
                            }
                            break;
                        default:
                            $query->where($column, $this->operatorMap[$operator], $requestParam[$operator]);
                            break;
                    }
                }
            }
        }

        return $query;
    }
}
