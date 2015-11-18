<?php


namespace model;


class MovieDateScraper
{
    public static function findRootPages($url){
        $values = array();

        $scrape = new WebScraper();
        $data = $scrape->get($url)->find('//a')->getData();

        if($data->length != 3){
            throw new \ScraperException("Could not find base links on page");
        }

        if($scrape->getInfo()['http_code'] != 200){
            throw new \ScraperException("Failed getting a proper response from server @\"{$url}\"");
        }

        foreach($data as $node){
            $value = str_replace('/', '', $node->getAttribute("href"));
            $values[$value] = \URL::concatenate($url, $value);
        }

        return $values;
    }

    public static function scanCalendar($url){
        $scrape = new WebScraper();
        $data = $scrape->get($url)->find('//a')->getData();

        if($scrape->getInfo()['http_code'] != 200){
            throw new \ScraperException("Failed getting a proper response from server when scanning calendars @\"{$url}\"");
        }

        $calendars = array();

        foreach($data as $node){
            /** @var $node \DOMElement  */

            $scrape->get(\URL::concatenate($url, $node->getAttribute("href")));
            $days = $scrape->find('//table//th')->getData();

            $available = $scrape->find('//table//td')->getData();
            $person = $scrape->find('//h2')->getData();
            $person = $person->item(0)->textContent;

            $calendars[$person] = array();
            for($i = 0; $i < $days->length; $i++){
                $calendars[$person][$days->item($i)->textContent] = $available->item($i)->textContent;
            }

        }
        return $calendars;
    }

    public static function scanCinema($url, $dates){
        $scrape = new WebScraper();
        $data = $scrape->get($url)->find('//select[@id="movie"]//option[@value]')->getData();

        if($scrape->getInfo()['http_code'] != 200){
            throw new \ScraperException("Failed getting a proper response from server when scanning cinema @\"{$url}\"");
        }

        $movies = array();

        foreach($data as $node){
            /** @var $node \DOMElement  */
            $movies[$node->getAttribute("value")] = $node->textContent;
        }

        $datesFromCinema = $scrape->get($url)->find('//select[@id="day"]//option[@value]')->getData();
        $scrape->reset();

        foreach($dates as $date => $status){
            $dates[$date] = array();
            $dateID = 0;
            foreach($datesFromCinema as $movieDate){
                $dateString = '';

                switch($movieDate->textContent){
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
                $results = $scrape->get(\URL::concatenate($url, 'check?day=' . $dateID . '&movie=' . $movieId))->getData();
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

    public static function scanRestaurant($url){
        $scrape = new WebScraper();
        $data = $scrape->get(\URL::concatenate($url))->find('//input[@type="radio"]')->getData();
        $date = array();

        if($scrape->getInfo()['http_code'] != 200){
            throw new \ScraperException("Failed getting a proper response from server when scanning restaurant @\"{$url}\"");
        }

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

    public static function BookRestaurant($username, $password, $url, $root, \model\Date $date)
    {
        $scrape = new WebScraper();
        $data = $scrape->get($url)->find('//form[@method="post"]')->getData();

        $scrape->reset();

        $post = $scrape->post(\URL::concatenate($root, $data[0]->getAttribute("action")), array(
            'username=' . $username,
            'password=' . $password,
            'submit=login',
            'group1=' . $date->dinner->value
        ))->getData();

        if(isset($scrape->getInfo()['http_code']) && $scrape->getInfo()['http_code'] == 200){
            return $post;
        }else{
            throw new \ScraperException("Failed getting a proper response from server when booking a table @\"{$url}\"");
        }
    }

}