<?php
namespace Utils\Database;

/**
 * Simple query builder
 * Usage example:
 *  $query = QueryBuilder::instance()
 *            ->select("COUNT(*)")
 *            ->from("PowerTrail")
 *            ->where()
 *              ->in("status", $statusIn)
 *            ->build();
 *
 * or
 *
 * $query = QueryBuilder::instance()
 *            ->select("*")
 *            ->from("PowerTrail")
 *            ->where()
 *              ->eq("status", CacheSet::STATUS_OPEN)
 *            ->limit($limit, $offset)
 *            ->build();
 */

class QueryBuilder
{

    private $method;

    private $fromTable;
    private $wheres = [];
    private $joins = [];

    private $columns = "*";

    private $limit = null;
    private $offset = null;


    public function __construct()
    {

    }

    public static function instance()
    {
        return new self();
    }


    public function select($columns=null){
        $this->method = "SELECT";
        if(!is_null($columns)){

            if(is_array($columns)){
                $this->columns = implode(',',$columns);
            }else{
                $this->columns = $columns;
            }
        }
        return $this;
    }

    public function from($tableName){
        $this->fromTable = $tableName;
        return $this;
    }

    public function join($tableName, $onStr=null)
    {
        $this->joins[] = "$tableName ON $onStr";
        return $this;
    }

    public function where($column=null, $value=null){
        if($column && $value){
            $this->eq($column, $value);
        }
        return $this;
    }

    public function eq($column, $value)
    {
        $str = "$column = $value";
        $this->wheres[] = self::escapeStr($str);
        return $this;
    }

    public function in($column, array $arrayOfValues)
    {
        if(!empty($arrayOfValues)){
            $queryStr = self::escapeStr(implode(',',$arrayOfValues));
            $this->where[] = "$column IN ($queryStr)";
        }
        return $this;
    }

    public function limit($limit=null, $offset=null)
    {
        $this->limit = self::getIntValOrNull($limit);
        $this->offset = self::getIntValOrNull($offset);
        return $this;
    }

    private function getFromString()
    {
        $result = " FROM ".$this->fromTable;
        foreach ($this->joins as $join){
            $result .= " JOIN $join";
        }
        return $result;
    }

    private function getWhereString()
    {
        if(!empty($this->where)){
            return ' WHERE '.implode(' AND ', $this->where);
        }
        return '';
    }

    private function getLimitString()
    {
        $result = "";
        if(!is_null($this->limit)){
            $result .= " LIMIT ".$this->limit;
        }
        if(!is_null($this->offset)){
            $result .= " OFFSET ".$this->offset;
        }
        return $result;
    }

    public function build(){
        $result = $this->method;
        $result .= " ".$this->columns;
        $result .= $this->getFromString();
        $result .= $this->getWhereString();
        $result .= $this->getLimitString();
        return $result;
    }


    private static function getIntValOrNull($var)
    {
        return (is_int($var)?intval($var):null);
    }

    private static function escapeStr($str)
    {
        return XDb::xEscape($str);
    }
}

