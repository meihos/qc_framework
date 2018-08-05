<?php
namespace Core\Sql\Repository;

/**
 * Class Filter
 * @package Core\Sql\Repository
 */
class Filter
{

    protected $filters;

    public function __construct()
    {
        $this->filters = [];
    }

    private function initFilterSection($name)
    {
        if (!empty($name)) {
            if (!isset($this->filters[$name])) {
                $this->filters[$name] = [];
            }
        }
    }

    /**
     * @param $count
     * @param int $start
     * @return $this
     */
    public function setLimitFilter($count, $start = 0)
    {

        if (!empty($count)) {

            $this->initFilterSection('limit');

            $this->filters['limit']['start'] = $start;
            $this->filters['limit']['count'] = $count;
        }

        return $this;
    }

    /**
     * @param $column
     * @param string $direction
     * @param bool $index
     * @return $this
     */
    public function setOrderFilter($column, $direction = 'desc', $index = false)
    {
        if (!empty($column)) {

            $this->initFilterSection('orderBy');

            if ($index === false) {
                $this->filters['orderBy'][] = [
                    'column' => $column,
                    'direction' => $direction,
                ];
            } else {
                $this->filters['orderBy'][$index] = [
                    'column' => $column,
                    'direction' => $direction,
                ];
            }
        }

        return $this;
    }

    /**
     * @param $column
     * @param null $value
     * @param string $method
     * @param string $connector
     * @return $this
     */
    public function setWhereFilter($column, $value = null, $method = '=', $connector = 'AND')
    {
        if ((!empty($column)) && (!is_null($value))) {

            $this->initFilterSection('where');

            $this->filters['where'][] = [
                'column' => $column,
                'value' => $value,
                'method' => $method,
                'connector' => $connector,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLimitFilter()
    {
        if (isset($this->filters['limit'])) {
            if ((isset($this->filters['limit']['start'])) && (isset($this->filters['limit']['count']))) {
                return $this->filters['limit'];
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function getOrderFilter()
    {
        if ((!isset($this->filters['orderBy'])) || (!is_array($this->filters['orderBy']))) {
            return [];
        }

        $return = [];
        foreach ($this->filters['orderBy'] as $params) {
            if ((isset($params['column'])) && (isset($params['direction']))) {
                $return[] = $params;
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getWhereFilter()
    {
        if ((!isset($this->filters['where'])) || (!is_array($this->filters['where']))) {
            return [];
        }

        $return = [];
        foreach ($this->filters['where'] as $params) {
            if ((isset($params['column'])) && (isset($params['value']))) {
                $return[] = $params;
            }
        }

        return $return;
    }
} 