<?php

namespace EmoG\KendoGrid\Adapters;

use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Adapter\QueryBuilder as PQueryBuilder;

/**
 * Class QueryBuilder
 * @package EmoG\KendoGrid\Adapters
 */
class QueryBuilder extends AdapterInterface
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param $builder
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return array|mixed
     */
    public function getResponse()
    {
        $this->bind('column_search', function ($column) {
            $column     = $this->getFilterDbOperators($column);
            $key        = "key_{$column['field']}_" . $this->getRandomString();
            $columnName = !in_array($column['field'], array_keys($this->customColumns)) ? $column['field'] : $this->customColumns[$column['field']];

            $this->builder->andWhere("{$columnName} {$column['dbOperator']} :{$key}:", [$key => $column['searchValue']]);
        });

        $this->bind('order', function ($order) {
            $customColumnsKeys = array_keys($this->customColumns);
            $filteredOrders    = [];
            for ($i = 0; $i < count($order); $i++) {
                $column           = explode(' ', trim($order[$i]));
                $columnName       = !in_array($column[0], $customColumnsKeys) ? $column[0] : $this->customColumns[$column[0]];
                $filteredOrders[] = $columnName . ' ' . $column[1];
            }
            $this->builder->orderBy(implode(', ', $filteredOrders));
        });

        $builder = new PQueryBuilder([
            'builder' => $this->builder,
            'limit'   => $this->parser->getLimit(),
            'page'    => $this->parser->getPage(),
        ]);

        $filtered = $builder->getPaginate();
        return $this->formResponse([
            'total' => $filtered->total_items,
            'data'  => $filtered->items->toArray(),
        ]);
    }
}
