<?php


namespace controller;


class WebScraper
{
    private $url;

    public function __construct($url){
        $this->url = $url;
    }

    public function findAvailable(){
        return array();
    }
}