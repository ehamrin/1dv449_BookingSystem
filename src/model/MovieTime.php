<?php


namespace model;


class MovieTime
{
    public $start;

    public function __construct($name, $id, $date, $start)
    {
        $this->start = $start;
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
    }
}