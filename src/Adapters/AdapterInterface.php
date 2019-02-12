<?php

namespace EmoG\KendoGrid\Adapters;

use EmoG\KendoGrid\ParamsParser;

/**
 * @property ParamsParser $parser
 */
abstract class AdapterInterface
{
    protected $parser        = null;
    protected $columns       = [];
    protected $customColumns = [];

    protected $length = 30;

    /**
     * AdapterInterface constructor.
     * @param $length
     */
    public function __construct($length)
    {
        $this->length = $length;
    }

    /**
     * @return mixed
     */
    abstract public function getResponse();

    /**
     * @param ParamsParser $parser
     */
    public function setParser(ParamsParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getCustomColumns()
    {
        return $this->customColumns;
    }

    /**
     * @param $customColumns
     */
    public function setCustomColumns($customColumns)
    {
        $this->customColumns = $customColumns;
    }

    /**
     * @param $column
     * @return bool
     */
    public function columnExists($column)
    {
        return in_array($column, $this->columns);
    }

    /**
     * @param $column
     * @return bool
     */
    public function columnExistsInCustomColumns($column)
    {
        return in_array($column, array_keys($this->customColumns));
    }

    /**
     * @return ParamsParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param $options
     * @return array
     */
    public function formResponse($options)
    {
        $defaults = [
            'total' => 0,
            'data' => []
        ];
        $options  += $defaults;

        $response          = [];
        $response['total'] = $options['total'];

        if (count($options['data'])) {
            foreach ($options['data'] as $item) {
                $response['data'][] = $item;
            }
        } else {
            $response['data'] = [];
        }

        return $response;
    }

    /**
     * @param $string
     * @return string
     */
    public function sanitaze($string)
    {
        return mb_substr($string, 0, $this->length);
    }

    /**
     * @param int $length
     * @return bool|string
     */
    public function getRandomString($length = 7)
    {
        return substr(md5(rand()), 0, $length);
    }

    /**
     * @param $case
     * @param $closure
     * @throws \InvalidArgumentException
     */
    public function bind($case, $closure)
    {
        switch ($case) {
            case "column_search":
                $columnSearch = $this->parser->getColumnsSearch();
                if (!$columnSearch) {
                    return;
                }
                foreach ($columnSearch as $key => $column) {
                    if (!$this->columnExists($column['field']) && !$this->columnExistsInCustomColumns($column['field'])) {
                        continue;
                    }
                    $closure($column);
                }
                break;
            case "order":
                $order = $this->parser->getOrder();
                if (!$order) {
                    return;
                }

                $orderArray = [];

                foreach ($order as $orderBy) {
                    if (!isset($orderBy['dir']) || !isset($orderBy['field'])) {
                        continue;
                    }
                    $orderDir = mb_strtoupper($orderBy['dir']);
                    if (!$this->columnExists($orderBy['field']) && !$this->columnExistsInCustomColumns($orderBy['field'])) {
                        continue;
                    }
                    $orderArray[] = "{$orderBy['field']} {$orderDir}";
                }
                if (empty($orderArray)) {
                    return;
                }
                $closure($orderArray);
                break;
            default:
                throw new \InvalidArgumentException('Unknown bind type');
        }
    }

    /**
     * @param $column
     * @return mixed
     */
    public function getFilterDbOperators($column)
    {
        $column['searchValue'] = $column['value'];
        switch ($column['operator']) {
            case "contains":
                $column['searchValue'] = "%{$column['value']}%";
                $column['dbOperator']  = "LIKE";
                break;
            case "doesnotcontain":
                $column['searchValue'] = "%{$column['value']}%";
                $column['dbOperator']  = "NOT LIKE";
                break;
            case "startswith":
                $column['searchValue'] = "{$column['value']}%";
                $column['dbOperator']  = "LIKE";
                break;
            case "endswith":
                $column['searchValue'] = "%{$column['value']}";
                $column['dbOperator']  = "LIKE";
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

    /**
     * @param $itemValue
     * @param $searchValue
     * @param $operator
     * @return bool
     */
    public function checkCondition($itemValue, $searchValue, $operator)
    {
        if (!is_numeric($itemValue) && !is_numeric($searchValue)) {
            $itemValue   = strtolower($itemValue);
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
                return ($searchValue != '' && strpos($itemValue, $searchValue) === 0);
                break;
            case "endswith":
                return ((string)$searchValue === substr($itemValue, -strlen($searchValue)));
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
