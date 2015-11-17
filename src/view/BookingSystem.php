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
    <input type="url" name="' . self::$formRootURL . '" placeholder="Ange URL"/>
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
        $options = '<h1>Välj en passande tid</h1>
            <form action="" method="POST">';

        if(isset($_POST['do_reservation']) && !isset($_POST['date'])){
            $options .= '<p class="error">Du måste välja ett alternativ</p>';
        }

        $prev = "";
        foreach($data as $id => $date){
            /** @var $date \model\Date */
            if($date->movie->date != $prev){
                switch($date->movie->date){
                    case 'Friday':
                        $options .= '<h2>Fredag</h2>';
                        break;
                    case 'Saturday':
                        $options .= '<h2>Lördag</h2>';
                        break;
                    case 'Sunday':
                        $options .= '<h2>Söndag</h2>';
                        break;
                }
                $prev = $date->movie->date;
            }
            $options .= $this->renderDate($date, $id);
        }
        $options .= '<button type="submit" name="do_reservation">Boka!</button></form>';
        return $options;
    }

    public function setSuccessfulBooking($message){
        return '<p class="success">Din boking genomfördes med meddelande: <em>' . $message . '</em></p>' . $this->renderDate($this->getDate());
    }

    private function renderDate(\model\Date $date, $inForm = false){
        $return = '';
        $return .= $inForm !== false ? '<label>' : '';
        $return .= '<div class="date inline-1-4">';
        $return .= $inForm !== false ? '<input type="radio" value="' . $inForm . '" name="date" class="check-hidden" />' : '';
        $return .= '
                <div class="date-info">
                    <h3>' . $date->movie->name . '</h3>
                    <p><strong>Tid:</strong><br />' . date('H:i', $date->start) . '-' . date('H:i', $date->end) . '</p>
                    <p>
                        <strong>Uppskattad väntetid:</strong><br />' . $date->waiting . 'min
                    </p>
                    <p>
                        <strong>Middag innan bio:</strong><br />' . ($date->dinnerBefore ? 'Ja' : 'Nej') . '
                    </p>
                    <p>
                        <strong>Middag:</strong><br />' . $date->dinner->start . ' - ' . $date->dinner->end . '
                    </p>
                    <p>
                        <strong>Bion börjar:</strong><br />' . $date->movie->start . '
                    </p>
                </div>';
        $return .= $inForm !== false ? '</label>' : '';
        $return .= '</div>';

        return $return;
    }
}