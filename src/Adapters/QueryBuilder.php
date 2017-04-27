<?php
namespace EmoG\KendoGrid\Adapters;

use App\Library\Utils;
use Phalcon\Paginator\Adapter\QueryBuilder as PQueryBuilder;

class QueryBuilder extends AdapterInterface
{
    protected $builder;

    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    public function getResponse()
    {

        $this->bind('column_search', function ($column) {
            $column = $this->getFilterDbOperators($column);
            $key = "key_{$column['field']}_" . $this->getRandomString();
            $columnName = !in_array($column['field'], array_keys($this->customColumns)) ? $column['field'] : $this->customColumns[$column['field']];
            $this->builder->andWhere("{$columnName} {$column['dbOperator']} :{$key}:", [$key => $column['searchValue']]);
        });

        $this->bind('order', function ($order) {
            $this->builder->orderBy(implode(', ', $order));
        });

        $builder = new PQueryBuilder([
            'builder' => $this->builder,
            'limit' => $this->parser->getLimit(),
            'page' => $this->parser->getPage(),
        ]);

        $filtered = $builder->getPaginate();
        return $this->formResponse([
            'total' => $filtered->total_items,
            'data' => $filtered->items->toArray(),
        ]);
    }
}