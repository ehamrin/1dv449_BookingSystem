<?php


namespace controller;


class WebScraper
{
    private $data;

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
            $this->data = $xpath->query($query);
        }

        //Enable chaining
        return $this;
    }

    /**
     * @return \DOMNodeList[]
     */
    public function getData(){
        return $this->data;
    }
}