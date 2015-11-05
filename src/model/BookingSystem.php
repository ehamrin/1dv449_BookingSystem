<?php


namespace model;


class BookingSystem
{
    public function setRoot($url){
        $_SESSION['root_url'] = $url;
    }

    public function hasRootPage(){
        return isset($_SESSION['root_url']);
    }

    public function getRootPage(){
        return $_SESSION['root_url'];
    }

    public function reset(){
        unset($_SESSION['root_url']);
    }
}