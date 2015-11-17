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

    public function submittedDate(){
        return isset($_REQUEST['date']);
    }

    public function getDate(){
        $data = $this->model->getAvailableMovieDinners();
        return $data[$_REQUEST['date']];
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

    public function showAvailable(){
        $data = $this->model->getAvailableMovieDinners();
        $options = '<form action="" method="POST">';
        $prev = "";
        foreach($data as $id => $date){
            /** @var $date \model\Date */
            if($date->movie->date != $prev){
                $options .= '<h2>' . $date->movie->date . '</h2>';
                $prev = $date->movie->date;
            }
            $options .= '
        <label>
            <div class="date-info inline-1-4">
                <input type="radio" value="' . $id . '" name="date" />
                <h3>' . $date->movie->name . ' | ' . date('H:i', $date->start) . '-' . date('H:i', $date->end) . '</h3>
                <p>
                    Väntetid mellan film/middag: ' . $date->waiting . 'min
                </p>
                <p>
                    Middag innan bio: ' . ($date->dinnerBefore ? 'Ja' : 'Nej') . '
                </p>
                <p>
                    Middag: ' . $date->dinner->start . ' - ' . $date->dinner->end . '
                </p>
                <p>
                    Bion börjar: ' . $date->movie->start . '
                </p>
            </div>
        </label>';
        }
        $options .= '<button type="submit" name="do_reservation">Boka!</button></form>';
        return $options;
    }
}