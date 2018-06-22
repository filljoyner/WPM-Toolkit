<?php namespace WpmPack\Test;

class WpmTest
{
    protected $results = [];


    public function getResults()
    {
        return $this->results;
    }


    public function result($result)
    {
        $traces = debug_backtrace();
        $trace = $traces[1];
        $testTrace = $traces[2];

        $this->results[] = [
            'pass' => $result,
            'data' => [
                'class'     => $testTrace['class'],
                'method'    => $testTrace['function'],
                'test'      => $trace['function'],
                'line'      => $trace['line'],
                'args'      => $trace['args']
            ]
        ];
    }


    public function isTrue($val) {
        if($val === true) return $this->result(true);
        return $this->result(false);
    }

    public function isFalse($val) {
        if($val === false) return $this->result(true);
        return $this->result(false);
    }


    public function isEmpty($val) {
        if(empty($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isNotEmpty($val) {
        if(!empty($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isNumeric($val)
    {
        if(is_numeric($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isString($val)
    {
        if(is_string($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isArray($val)
    {
        if(is_array($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isObject($val)
    {
        if(is_object($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isInteger($val)
    {
        if(is_integer($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isNull($val)
    {
        if(is_null($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isBool($val)
    {
        if(is_bool($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isFloat($val)
    {
        if(is_float($val)) return $this->result(true);
        return $this->result(false);
    }


    public function isEqual($valA, $valB) {
        if($valA === $valB) return $this->result(true);
        return $this->result(false);
    }


    public function isCountEqual($valA, $valB) {
        if(count($valA) === $valB) return $this->result(true);
        return $this->result(false);
    }


    public function notEqual($valA, $valB) {
        if($valA !== $valB) return $this->result(true);
        return $this->result(false);
    }


    public function notCountEqual($valA, $valB) {
        if(count($valA) !== $valB) return $this->result(true);
        return $this->result(false);
    }


    public function isMore($valA, $valB=0)
    {
        if($valA > $valB) return $this->result(true);
        return $this->result(false);
    }


    public function isCountMore($valA, $valB=0) {
        if(count($valA) > $valB) return $this->result(true);
        return $this->result(false);
    }


    public function isLess($valA, $valB=0) {
        if($valA < $valB) return $this->result(true);
        return $this->result(false);
    }


    public function isCountLess($valA, $valB=0) {
        if(count($valA) < $valB) return $this->result(true);
        return $this->result(false);
    }


    public function isIn($valA, $valB) {
        if(in_array($valA, $valB)) return $this->result(true);
        return $this->result(false);
    }


    public function isNotIn($valA, $valB) {
        if(!in_array($valA, $valB)) return $this->result(true);
        return $this->result(false);
    }


    public function strContains($str, $find) {
        if (strpos($str, $find) !== false) return $this->result(true);
        return $this->result(false);
    }


    public function strNotContains($str, $find) {
        if (strpos($str, $find) == false) return $this->result(true);
        return $this->result(false);
    }
}