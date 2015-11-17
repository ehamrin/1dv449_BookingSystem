<?php


namespace model;


class Date
{
    public $movie;
    public $dinner;
    public $waiting;
    public $start;
    public $end;
    public $dinnerBefore;

    public function __construct(DinnerTime $dinner, MovieTime $movie, $waiting, $start, $end, $dinnerBefore)
    {
        $this->dinner = $dinner;
        $this->movie = $movie;
        $this->waiting = $waiting;
        $this->start = $start;
        $this->end = $end;
        $this->dinnerBefore = $dinnerBefore;

    }
}