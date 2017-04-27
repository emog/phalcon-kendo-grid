<?php

namespace EmoG\KendoGrid\Adapters;

use EmoG\KendoGrid\ParamsParser;

abstract class AdapterInterface
{

    protected $parser = null;
    protected $columns = [];
    protected $customColumns = [];

    protected $length = 30;

    public function __construct($length)
    {
        $this->length = $length;
    }

    abstract public function getResponse();

    public function setParser(ParamsParser $parser)
    {
        $this->parser = $parser;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getCustomColumns()
    {
        return $this->customColumns;
    }

    public function setCustomColumns($customColumns)
    {
        $this->customColumns = $customColumns;
    }

    public function columnExists($column)
    {
        return in_array($column, $this->columns);
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function formResponse($options)
    {
        $defaults = [
            'total' => 0,
            'data' => []
        ];
        $options += $defaults;

        $response = [];
        $response['total'] = $options['total'];

        if (count($options['data'])) {
            foreach ($options['data'] as $item) {
                if (isset($item['id'])) {
                    $item['DT_RowId'] = $item['id'];
                }

                $response['data'][] = $item;
            }
        } else {
            $response['data'] = [];
        }

        return $response;
    }

    public function sanitaze($string)
    {
        return mb_substr($string, 0, $this->length);
    }

    public function getRandomString($length = 7)
    {
        return substr(md5(rand()), 0, $length);
    }

    public function bind($case, $closure)
    {
        switch ($case) {
            case "column_search":
                $columnSearch = $this->parser->getColumnsSearch();
                if (!$columnSearch) return;
                foreach ($columnSearch as $key => $column) {
                    if (!$this->columnExists($column['field'])) continue;
                    $closure($column);
                }
                break;
            case "order":
                $order = $this->parser->getOrder();
                if (!$order) return;

                $orderArray = [];

                foreach ($order as $orderBy) {
                    if (!isset($orderBy['dir']) || !isset($orderBy['field'])) continue;
                    $orderDir = mb_strtoupper($orderBy['dir']);
                    if (!$this->columnExists($orderBy['field'])) continue;
                    $orderArray[] = "{$orderBy['field']} {$orderDir}";
                }
                if (empty($orderArray)) return;
                $closure($orderArray);
                break;
            default:
                throw new \Exception('Unknown bind type');
        }

    }

    public function getFilterDbOperators($column)
    {
        $column['searchValue'] = $column['value'];
        switch ($column['operator']) {
            case "contains":
                $column['searchValue'] = "%{$column['value']}%";
                $column['dbOperator'] = "LIKE";
                break;
            case "doesnotcontain":
                $column['searchValue'] = "%{$column['value']}%";
                $column['dbOperator'] = "NOT LIKE";
                break;
            case "startswith":
                $column['searchValue'] = "{$column['value']}%";
                $column['dbOperator'] = "LIKE";
                break;
            case "endswith":
                $column['searchValue'] = "%{$column['value']}";
                $column['dbOperator'] = "LIKE";
                break;
            case "eq":
                $column['dbOperator'] = "=";
                break;
            case "neq":
                $column['dbOperator'] = "<>";
                break;
            case "gt":
                $column['dbOperator'] = ">";
                break;
            case "gte":
                $column['dbOperator'] = ">=";
                break;
            case "lt":
                $column['dbOperator'] = "<";
                break;
            case "lte":
                $column['dbOperator'] = "<=";
                break;
        }
        return $column;
    }

    public function checkCondition($itemValue, $searchValue, $operator)
    {
        if (!is_numeric($itemValue) && !is_numeric($searchValue)) {
            $itemValue = strtolower($itemValue);
            $searchValue = strtolower($searchValue);
        };
        switch ($operator) {
            case "contains":
                return strpos($itemValue, $searchValue) !== false;
                break;
            case "doesnotcontain":
                return strpos($itemValue, $searchValue) === false;
                break;
            case "startswith":
                break;
            case "endswith":
                break;
            case "eq":
                return $itemValue == $searchValue;
                break;
            case "neq":
                return $itemValue != $searchValue;
                break;
            case "gt":
                return $itemValue > $searchValue;
                break;
            case "gte":
                return $itemValue >= $searchValue;
                break;
            case "lt":
                return $itemValue < $searchValue;

                break;
            case "lte":
                return $itemValue <= $searchValue;
                break;
        }
        return false;
    }

}
