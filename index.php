<?php
require_once 'src/controller/BookingSystem.php';
require_once 'src/view/HTMLTemplate.php';

$booking = new controller\BookingSystem();
$template = new view\HTMLTemplate();
echo $template->render($booking->getView());