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
            $this->output = $this->view->showAvailable($scrape->findAvailable());
        }
    }

    public function getView(){
        return $this->output;
    }





}