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
</head>
<body>
    {$body}
</body>
</html>
HTML;

    }
}