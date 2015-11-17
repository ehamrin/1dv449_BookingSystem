<?php
//error_reporting(E_ALL);
ini_set('display_errors', 0);

function debug($data){
    echo '<div class="info debugging"><pre>';
    var_dump($data);
    echo '</pre></div>';
}

spl_autoload_register(function ($class) {
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    $filename = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php';
    if(file_exists($filename)){
        require_once $filename;
    }
});



session_start();

$booking = new controller\BookingSystem('zeke', 'coys', 15);
$template = new view\HTMLTemplate();
echo $template->render($booking->getView());


