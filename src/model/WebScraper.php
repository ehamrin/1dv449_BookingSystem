<?php


namespace model;


class WebScraper
{
    private $data = null;
    private $foundResult = null;
    private $info = array();

    private static $userAgent = '{"Name": "Robot BookingSystem", "Location": "http://www.erikhamrin.se", "Email": "kontakt@erikhamrin.se"}';

    /**
     * @param $url
     * @return WebScraper
     */
    public function get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$userAgent);
        $data = curl_exec($ch);
        $this->info = curl_getinfo($ch);
        curl_close($ch);
        $this->data = $data;

        //Enable chaining
        return $this;
    }

    /**
     * @param $url
     * @param array $post
     * @return WebScraper
     */
    public function post($url, $post = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_POST, count($post));
        curl_setopt($ch,CURLOPT_POSTFIELDS, implode('&',$post));
        curl_setopt($ch, CURLOPT_USERAGENT, self::$userAgent);
        $data = curl_exec($ch);
        $this->info = curl_getinfo($ch);
        curl_close($ch);
        $this->data = $data;

        //Enable chaining
        return $this;
    }

    /**
     * @param $query
     * @return WebScraper
     */
    public function find($query){
        $dom = new \DOMDocument();
        if(empty($this->data)){
            throw new \BadFunctionCallException("There is no data to load");
        }
        if($dom->loadHTML($this->data)){
            $xpath = new \DOMXPath($dom);
            $this->foundResult = $xpath->query($query);
        }

        //Enable chaining
        return $this;
    }

    /**
     * @return \DOMNodeList
     */
    public function getData(){
        if($this->foundResult){
            return $this->foundResult;
        }

        return $this->data;
    }

    public function getInfo(){
        return $this->info;
    }
    public function reset(){
        $this->foundResult = null;
        $this->info = array();
    }
}