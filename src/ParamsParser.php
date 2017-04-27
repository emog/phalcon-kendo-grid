<?php
namespace EmoG\KendoGrid;

use Phalcon\Mvc\User\Component;

class ParamsParser extends Component
{

    protected $params = [];
    protected $page = 1;

    public function __construct($limit)
    {
        $params = [
            'skip' => 0,
            'pageSize' => $limit,
            'filter' => [],
            'search' => [],
            'order' => []
        ];

        $request = $this->di->get('request');
        $requestParams = $request->isPost() ? $request->getPost() : $request->getQuery();
        $this->params = (array)$requestParams + $params;
        $this->setPage();
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setPage()
    {
        $this->page = (int)(floor($this->params['skip'] / $this->params['pageSize']) + 1);
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getColumnsSearch()
    {
        return array_filter(array_map(function ($item) {
            return (isset($item['field']) && strlen($item['value'])) ? $item : null;
        }, $this->params['filter']['filters']));
    }

    public function getSearchableColumns()
    {
        return array_filter(array_map(function ($item) {
            return (isset($item['searchable']) && $item['searchable'] === "true") ? $item['data'] : null;
        }, $this->params['columns']));
    }

    public function getLimit()
    {
        return $this->params['pageSize'];
    }

    public function getOffset()
    {
        return $this->params['skip'];
    }

    public function getColumns()
    {
        return $this->params['columns'];
    }

    public function getColumnById($id)
    {
        return isset($this->params['columns'][$id]['data']) ? $this->params['columns'][$id]['data'] : null;
    }

    public function getSearch()
    {
        return $this->params['search'];
    }

    public function getOrder()
    {
        return $this->params['sort'];
    }

    public function getSearchValue()
    {
        return isset($this->params['search']['value']) ? $this->params['search']['value'] : '';
    }
}
