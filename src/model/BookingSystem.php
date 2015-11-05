<?php


namespace model;


class BookingSystem
{
    private $calendar;

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

    public function saveRootNode($name, $value){

    }

    public function setCalendars(array $calendar){
        $this->calendar = $calendar;
    }

    /**
     * @return array
     */
    public function getFreeDates(){
        $freeDate = array();
        $first = true;
        foreach($this->calendar as $person => $array){
            foreach($array as $day => $status){
                $status = trim(strtolower($status));
                if($first && $status == 'ok'){
                    $freeDate[$day] = 'ok';
                }elseif(isset($freeDate[$day]) && $status != 'ok'){
                    unset($freeDate[$day]);
                }
            }
            $first = false;

        }
        return $freeDate;
    }
}