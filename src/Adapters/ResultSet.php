<?php
namespace EmoG\KendoGrid\Adapters;

class ResultSet extends AdapterInterface
{

    protected $resultSet;
    protected $column = [];
    protected $global = [];
    protected $order = [];

    public function getResponse()
    {
        $limit = $this->parser->getLimit();
        $offset = $this->parser->getOffset();
        $total = $this->resultSet->count();

        $this->bind('column_search', function ($column) {
            $this->column[$column['field']][] = array('searchValue' => $column['value'], 'operator' => $column['operator']);
        });

        $this->bind('order', function ($order) {
            $this->order = $order;
        });

        if (count($this->global) || count($this->column)) {
            $filter = $this->resultSet->filter(function ($item) {
                $check = false;

                if (count($this->global)) {
                    foreach ($this->global as $column => $filters) {
                        foreach ($filters as $search) {
                            $check = ($this->checkCondition($item[$column], $search['searchValue'], $search['operator']) !== false);
                            if ($check) break 2;
                        }
                    }
                } else {
                    $check = true;
                }

                if (count($this->column) && $check) {
                    foreach ($this->column as $column => $filters) {
                        foreach ($filters as $search) {
                            $check = ($this->checkCondition($item[$column], $search['searchValue'], $search['operator']) !== false);
                            if (!$check) break 2;
                        }
                    }
                }

                if ($check) {
                    return $item;
                }
            });

            $filtered = count($filter);
            $items = array_map(function ($item) {
                return $item->toArray();
            }, $filter);
        } else {
            $filtered = $total;
            $items = $this->resultSet->filter(function ($item) {
                return $item->toArray();
            });
        }

        if ($this->order) {
            $args = [];

            foreach ($this->order as $order) {
                $tmp = [];
                list($column, $dir) = explode(' ', $order);

                foreach ($items as $key => $item) {
                    $tmp[$key] = $item[$column];
                }

                $args[] = $tmp;
                $args[] = ($dir == 'DESC') ? SORT_DESC : SORT_ASC;
            }
            $args[] = &$items;
            call_user_func_array('array_multisort', $args);
        }

        if ($offset > 1) {
            $items = array_slice($items, ($offset - 1));
        }

        if ($limit) {
            $items = array_slice($items, 0, $limit);
        }

        return $this->formResponse([
            'total' => (int)$filtered,
            'data' => $items,
        ]);
    }

    public function setResultSet($resultSet)
    {
        $this->resultSet = $resultSet;
    }

}
