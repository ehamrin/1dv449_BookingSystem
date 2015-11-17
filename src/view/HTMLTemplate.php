<?php


namespace view;


class HTMLTemplate
{
    public function render($body){
        return <<<HTML
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>eh222ve - Booking System</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="wrapper">
    <div><a href="?reset">Nollställ</a></div>
    {$body}
    </div>
</body>
</html>
HTML;

    }
}