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
            $scrape = new WebScraper($this->model->getRootPage());
            $data = $scrape->get($this->model->getRootPage())->find('//a')->getData();

            foreach($data as $node){
                /** @var $node \DOMElement  */
                if(strpos($node->getAttribute("href"), 'calendar') !== false){

                    $url = $this->model->getRootPage();
                    $url = (substr($url,-1) == '/' ? rtrim($url, '/') : $url) . $node->getAttribute("href");
                    $url = substr($url,-1) != '/' ? $url . '/' : $url;
                    $this->scanCalendar($url);

                }elseif(strpos($node->getAttribute("href"), 'cinema') !== false){
                    $this->scanCinema();
                }elseif(strpos($node->getAttribute("href"), 'dinner') !== false){
                    $this->scanRestaurant();
                }
            }
            $this->output = $this->view->showAvailable($data);
        }
    }

    public function getView(){
        return $this->output;
    }

    private function scanCalendar($url){
        $scrape = new WebScraper();
        $data = $scrape->get($url . '/')->find('//a')->getData();
        var_dump($data);
        foreach($data as $node){
            /** @var $node \DOMElement  */
            echo '<br/>' . $node->nodeValue . '<br/>';
        }
    }

    private function scanCinema(){

    }

    private function scanRestaurant(){

    }





}