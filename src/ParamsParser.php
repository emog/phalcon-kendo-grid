<?php

namespace EmoG\KendoGrid;

use Phalcon\Mvc\User\Component;

/**
 * Class ParamsParser
 * @package EmoG\KendoGrid
 */
class ParamsParser extends Component
{
    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * ParamsParser constructor.
     *
     * @param $limit
     */
    public function __construct($limit)
    {
        $params = [
            'skip'     => 0,
            'pageSize' => $limit,
            'filter'   => ['filters' => []],
            'search'   => [],
            'sort'     => []
        ];

        $request       = $this->di->get('request');
        $requestParams = $request->isPost() ? $request->getPost() : $request->getQuery();
        $this->params  = array_merge($params, $requestParams);

        if (!isset($this->params['filter']['filters'])) {
            $this->params['filter']['filters'] = [];
        }

        $this->setPage();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     *
     */
    public function setPage()
    {
        $this->page = (int)(floor($this->params['skip'] / $this->params['pageSize']) + 1);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return array
     */
    public function getColumnsSearch()
    {
        return array_filter(array_map(function ($item) {
            return (isset($item['field']) && strlen($item['value'])) ? $item : null;
        }, $this->params['filter']['filters']));
    }

    /**
     * @return array
     */
    public function getSearchableColumns()
    {
        return array_filter(array_map(function ($item) {
            return (isset($item['searchable']) && $item['searchable'] === "true") ? $item['data'] : null;
        }, $this->params['columns']));
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->params['pageSize'];
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->params['skip'];
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->params['columns'];
    }

    /**
     * @param $id
     *
     * @return string|null
     */
    public function getColumnById($id)
    {
        return isset($this->params['columns'][$id]['data']) ? $this->params['columns'][$id]['data'] : null;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->params['search'];
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->params['sort'];
    }

    /**
     * @return string
     */
    public function getSearchValue()
    {
        return isset($this->params['search']['value']) ? $this->params['search']['value'] : '';
    }
}
