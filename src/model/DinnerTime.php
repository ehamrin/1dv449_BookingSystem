<?php


namespace model;


class DinnerTime
{
    public $start;
    public $end;
    public $value;

    public function __construct($start, $end, $value)
    {
        $this->start = $start;
        $this->end = $end;
        $this->value = $value;

    }
}