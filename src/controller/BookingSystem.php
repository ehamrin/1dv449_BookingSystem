<?php


namespace controller;

class BookingSystem
{
    private $model;
    private $view;
    private $output;

    public function __construct(){
        $this->model = new \model\BookingSystem();
        $this->view = new \view\BookingSystem($this->model);

        $this->doControl();
    }

    public function doControl(){
        if($this->view->userReset()){
            $this->model->reset();
            $this->view->reset();
        }
        if($this->view->submittedRootPage()){
            $this->model->setRoot($this->view->getRoot());
        }

        if($this->model->hasRootPage() == false){
            $this->output = $this->view->showRootForm();
        }else{
            if($this->view->submittedDate()){
                debug($this->view->getDate());
            }else{

                $scrape = new WebScraper($this->model->getRootPage());
                $data = $scrape->get($this->model->getRootPage())->find('//a')->getData();

                foreach($data as $node){
                    /** @var $node \DOMElement  */

                    $url = $this->model->getRootPage();
                    $url = (substr($url,-1) == '/' ? rtrim($url, '/') : $url) . $node->getAttribute("href");
                    $url = substr($url,-1) != '/' ? $url . '/' : $url;
                    if(strpos($node->getAttribute("href"), 'calendar') !== false && !$this->model->hasCalendar()){
                        $this->model->setCalendars($this->scanCalendar($url));
                    }elseif(strpos($node->getAttribute("href"), 'cinema') !== false && !$this->model->hasCinema()){
                        $this->model->setCinema($this->scanCinema($url));
                    }elseif(strpos($node->getAttribute("href"), 'dinner') !== false && !$this->model->hasDinner()){
                        $this->model->setDinner($this->scanRestaurant($url));
                    }
                }
                $this->output = $this->view->showAvailable();
            }
        }
    }

    public function getView(){
        return $this->output;
    }

    private function scanCalendar($url){
        $scrape = new WebScraper();
        $data = @$scrape->get($url . '/')->find('//a')->getData();
        $calendars = array();

        foreach($data as $node){
            /** @var $node \DOMElement  */
            $scrape->get($url . $node->getAttribute("href"));
            $days = $scrape->find('//table//th')->getData();
            $available = $scrape->find('//table//td')->getData();
            $person = $scrape->find('/html/body/h2')->getData();

            $person = $person->item(0)->nodeValue;
            $calendars[$person] = array();
            for($i = 0; $i < $days->length; $i++){
                $calendars[$person][$days->item($i)->nodeValue] = $available->item($i)->nodeValue;
            }

        }

        return $calendars;
    }

    private function scanCinema($url){
        $scrape = new WebScraper();
        $data = $scrape->get($url . '/')->find('//select[@id="movie"]//option[@value]')->getData();
        $movies = array();

        foreach($data as $node){
             /** @var $node \DOMElement  */
            $movies[$node->getAttribute("value")] = $node->nodeValue;
        }

        $datesFromCinema = $scrape->get($url . '/')->find('//select[@id="day"]//option[@value]')->getData();
        $scrape->reset();

        $dates = $this->model->getFreeDates();

        foreach($dates as $date => $status){
            $dates[$date] = array();
            $dateID = 0;
            foreach($datesFromCinema as $movieDate){
                $dateString = '';

                switch($movieDate->nodeValue){
                    case 'Fredag':
                        $dateString = 'Friday';
                        break;
                    case 'Lördag':
                        $dateString = 'Saturday';
                        break;
                    case 'Söndag':
                        $dateString = 'Sunday';
                        break;
                }

                if($dateString == $date){
                    $dateID = $movieDate->getAttribute("value");
                }
            }

            foreach($movies as $movieId => $name){
                $results = $scrape->get($url . 'check?day=' . $dateID . '&movie=' . $movieId)->getData();
                $results = json_decode($results);
                foreach($results as $result){
                    if($result->status){
                        if(!isset($dates[$date])){
                            $dates[$date] = array();
                        }
                        $dates[$date][] = new \model\MovieTime($name, $result->movie, $date, $result->time);
                    }
                }
            }
        }
        return $dates;
    }

    private function scanRestaurant($url){
        $scrape = new WebScraper();
        $data = $scrape->get(\URL::concatenate($url))->find('//input[@type="radio"]')->getData();
        $date = array();

        foreach($data as $node){
            /** @var $node \DOMElement  */
            preg_match("/([a-z]{3})(\d{2})(\d{2})/", $node->getAttribute("value"), $matches );

            $dateString = "";
            switch($matches[1]){
                case 'fre':
                    $dateString = 'Friday';
                    break;
                case 'lor':
                    $dateString = 'Saturday';
                    break;
                case 'son':
                    $dateString = 'Sunday';
                    break;
            }

            if(!isset($date[$dateString])){
                $date[$dateString] = array();
            }

            $date[$dateString][] = new \model\DinnerTime($matches[2], $matches[3], $node->getAttribute("value"));

        }

        return $date;
    }





}