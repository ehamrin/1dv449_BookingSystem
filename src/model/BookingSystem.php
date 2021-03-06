<?php


namespace model;


class BookingSystem
{
    private $waiting;

    public function __construct($waiting = 0)
    {
        $this->waiting = $waiting;
    }

    public function scan(){
        if(!$this->hasRootPages()){
            $this->setRootPages(MovieDateScraper::findRootPages($this->getRootPage()));
        }

        if(!$this->hasCalendar()) {
            if(!isset($this->getRootPages()['calendar'])){
                throw new \ScraperException("Could not find calendar url");
            }
            $this->setCalendars(MovieDateScraper::scanCalendar($this->getRootPages()['calendar']));
        }

        if(!$this->hasCinema()){
            if(!isset($this->getRootPages()['cinema'])){
                throw new \ScraperException("Could not find cinema url");
            }
            $this->setCinema(MovieDateScraper::scanCinema($this->getRootPages()['cinema'], $this->getFreeDates()));
        }

        if(!$this->hasDinner()){
            if(!isset($this->getRootPages()['dinner'])){
                throw new \ScraperException("Could not find dinner url");
            }
            $this->setDinner(MovieDateScraper::scanRestaurant($this->getRootPages()['dinner']));
        }
    }

    public function bookRestaurant($username, $password, $date){
        return MovieDateScraper::BookRestaurant($username, $password, $this->getRootPages()['dinner'], $this->getRootPage(), $date);
    }

    public function setRoot($url){
        $_SESSION['root_url'] = $url;
    }

    public function hasRootPage(){
        return isset($_SESSION['root_url']);
    }

    private function hasCalendar(){
        return isset($_SESSION['calendar']);
    }

    private function hasCinema(){
        return isset($_SESSION['cinema']);
    }

    private function hasDinner(){
        return isset($_SESSION['dinner']);
    }

    private function getRootPage(){
        return $_SESSION['root_url'];
    }

    public function reset(){
        unset($_SESSION['root_url']);
        unset($_SESSION['root_pages']);
        unset($_SESSION['cinema']);
        unset($_SESSION['dinner']);
        unset($_SESSION['calendar']);
    }

    private function setCalendars(array $calendar){

        $_SESSION['calendar'] = $calendar;
    }

    private function setCinema(array $cinema){
        $_SESSION['cinema'] = $cinema;
    }

    private function setDinner(array $dinner){
        $_SESSION['dinner'] = $dinner;
    }

    /**
     * @return array
     */
    private function getFreeDates(){
        $freeDate = array();
        $first = true;

        foreach($_SESSION['calendar'] as $person => $array){
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

    public function getAvailableMovieDinners()
    {
        if(!isset($_SESSION['cinema'], $_SESSION['dinner']) || !is_array($_SESSION['cinema']) || !is_array($_SESSION['dinner'])){
            throw new \Exception("Dinner or cinema not set properly");
        }
        $movies = $_SESSION['cinema'];
        $dates = array();
        foreach($_SESSION['dinner'] as $dinnerDate => $dinnerTimes){
            if(isset($movies[$dinnerDate])){
                foreach($dinnerTimes as $dinnerTime){
                    foreach($movies[$dinnerDate] as $movie){
                        $dinnerStart = strtotime('2000-01-01 ' . $dinnerTime->start . ':00');
                        $dinnerEnd = strtotime('2000-01-01 ' . $dinnerTime->end . ':00');
                        $movieStart = strtotime('2000-01-01 ' . $movie->start);
                        $movieEnd = $movieStart + (60*60*2);
                        if(
                            $dinnerEnd + $this->waiting <= $movieStart ||
                            $movieEnd + $this->waiting <= $dinnerStart
                        ){
                            if($movieStart - $dinnerEnd > 0){
                                $waiting = $movieStart - $dinnerEnd;
                                $start = $dinnerStart;
                                $end = $movieEnd;
                                $dinnerBefore = true;
                            }else{
                                $waiting = $dinnerStart - $movieEnd;
                                $start = $movieStart;
                                $end = $dinnerEnd;
                                $dinnerBefore = false;
                            }
                            $waiting = $waiting/60;
                            $dates[] = new Date($dinnerTime, $movie, $waiting, $start, $end, $dinnerBefore);
                        }
                    }

                }
            }
        }
        return $dates;
    }

    private function setRootPages($findRootPages)
    {
        $_SESSION['root_pages'] = $findRootPages;
    }

    private function getRootPages()
    {
        return $_SESSION['root_pages'];
    }

    private function hasRootPages()
    {
        return isset($_SESSION['root_pages']);
    }
}