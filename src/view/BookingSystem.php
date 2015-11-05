<?php


namespace view;


class BookingSystem
{
    private $model;

    private static $formRootURL = 'root_url';

    public function __construct(\model\BookingSystem $model){
        $this->model = $model;
    }

    public function showRootForm(){
        return '
<form method="POST" action="">
    <input type="url" name="' . self::$formRootURL . '"/>
    <button type="submit">Starta</button>
</form>
';
    }

    public function submittedRootPage(){
        return isset($_REQUEST[self::$formRootURL]) && filter_var($_REQUEST[self::$formRootURL], FILTER_VALIDATE_URL);
    }

    public function userReset(){
        return isset($_GET['reset']);
    }

    public function reset(){
        header('Location: ' . $_SERVER["PHP_SELF"]);
        //var_dump($_SERVER);
        die();
    }

    public function getRoot(){
        return $_REQUEST[self::$formRootURL];
    }

    public function showAvailable(array $data){
        return 'Options will be presented..';
    }
}