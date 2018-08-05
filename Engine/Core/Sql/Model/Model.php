<?php
namespace Core\Sql\Model;

/**
 * Class Model
 * @package Core\Sql\Model
 */
class Model
{

    protected $__table;
    protected $__fields = [];
    protected $__pKeys = [];

    /**
     * @return mixed
     */
    public function __table()
    {
        return $this->__table;
    }

    /**
     * @return array
     */
    public function __fields()
    {
        return $this->__fields;
    }

    /**
     * @return array
     */
    public function __pKeys()
    {
        return $this->__pKeys;
    }



    public function fieldsToSql()
    {
        $sql = ['fields' => [], 'keys' => []];
        foreach ($this->__fields as $var => $sqlField) {
            if (!isset($this->$var)) {
                continue;
            }

            $key = (in_array($sqlField, $this->__pKeys)) ? 'keys' : 'fields';
            if (($key == 'keys') && (is_null($this->$var))) {
                continue;
            }

            $sql[$key][$sqlField] = $this->fieldValueToSql($this->$var);
        }


        return $sql;
    }

    private function fieldValueToSql($fieldValue)
    {
        if (is_array($fieldValue)) {
            return json_encode($fieldValue);
        }
        return $fieldValue;
    }

    /**
     * @param $sqlData
     * @return bool
     */
    public function fieldsFromSql($sqlData)
    {
        if (!empty($sqlData) && is_array($sqlData)) {
            $fields = array_flip($this->__fields);
            foreach ($sqlData as $key => $value) {
                if (isset($fields[$key])) {
                    if ($this->isJson($value)) {
                        $value = json_decode($value, true);
                    }
                    $fieldName = $fields[$key];
                    $this->$fieldName = $value;
                }
            }

            return true;
        }

        return false;
    }

    private function isJson($string)
    {
        $data = json_decode($string);
        if (($string != $data) && $data && (json_last_error() == JSON_ERROR_NONE)) {
            return true;
        }

        return false;
    }

}