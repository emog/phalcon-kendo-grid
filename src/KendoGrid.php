<?php

namespace EmoG\KendoGrid;

use EmoG\KendoGrid\Adapters\QueryBuilder;
use EmoG\KendoGrid\Adapters\ResultSet;
use EmoG\KendoGrid\Adapters\ArrayAdapter;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\User\Plugin;

/**
 * Class KendoGrid
 * @package EmoG\KendoGrid
 */
class KendoGrid extends Plugin
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $response;
    /**
     * @var ParamsParser
     */
    public $parser;

    /**
     * KendoGrid constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $default = [
            'limit'  => 20,
            'length' => 50,
        ];

        $this->options = $options + $default;
        $this->parser  = new ParamsParser($this->options['limit']);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->parser->getParams();
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return !empty($this->response) ? $this->response : [];
    }

    /**
     *
     */
    public function sendResponse()
    {
        if ($this->di->has('view')) {
            $this->di->get('view')->disable();
        }

        $response = new Response();
        $response->setContentType('application/json', 'utf8');
        $response->setJsonContent($this->getResponse());
        $response->send();
    }

    /**
     * @param $builder
     * @param array $columns
     * @param array $customColumns
     *
     * @return $this
     */
    public function fromBuilder(Builder $builder, $columns = [], $customColumns = [])
    {
        if (empty($columns)) {
            $columns = $builder->getColumns();
            $columns = (is_array($columns)) ? $columns : array_map('trim', explode(',', $columns));
        }

        $adapter = new QueryBuilder($this->options['length']);
        $adapter->setBuilder($builder);
        $adapter->setParser($this->parser);
        $adapter->setColumns($columns);
        $adapter->setCustomColumns($customColumns);
        $this->response = $adapter->getResponse();

        return $this;
    }

    /**
     * @param \Phalcon\Mvc\Model\Resultset $resultSet
     * @param array $columns
     *
     * @return $this
     */
    public function fromResultSet(\Phalcon\Mvc\Model\Resultset $resultSet, $columns = [])
    {
        if (empty($columns) && $resultSet->count() > 0) {
            $columns = array_keys($resultSet->getFirst()->toArray());
            $resultSet->rewind();
        }

        $adapter = new ResultSet($this->options['length']);
        $adapter->setResultSet($resultSet);
        $adapter->setParser($this->parser);
        $adapter->setColumns($columns);
        $this->response = $adapter->getResponse();

        return $this;
    }

    /**
     * @param array $array
     * @param array $columns
     *
     * @return $this
     */
    public function fromArray(array $array, $columns = [])
    {
        if (empty($columns) && count($array) > 0) {
            $columns = array_keys(current($array));
        }

        $adapter = new ArrayAdapter($this->options['length']);
        $adapter->setArray($array);
        $adapter->setParser($this->parser);
        $adapter->setColumns($columns);
        $this->response = $adapter->getResponse();

        return $this;
    }
}
