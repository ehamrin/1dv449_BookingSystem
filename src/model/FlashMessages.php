<?php


namespace model;


class FlashMessages
{

    public static function findAll()
    {
        if(!isset($_SESSION['messages'])){
            $_SESSION['messages'] = array();
        }
        $messages = $_SESSION['messages'];
        unset($_SESSION['messages']);
        return $messages;
    }

    private static function set(FlashMessages $message){
        if(!isset($_SESSION['messages'])){
            $_SESSION['messages'] = array();
        }
        $_SESSION['messages'][] = $message;
    }

    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
        self::set($this);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMessage()
    {
        return $this->message;
    }
}