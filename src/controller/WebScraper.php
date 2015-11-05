<?php


namespace controller;


class WebScraper
{
    private $data = null;
    private $foundResult = null;

    /**
     * @param $url
     * @return WebScraper
     */
    public function get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = curl_exec($ch);
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
        $data = curl_exec($ch);
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

    public function reset(){
        $this->foundResult = null;
    }
}