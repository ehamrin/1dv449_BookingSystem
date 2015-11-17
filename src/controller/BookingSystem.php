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
            if($this->view->submittedDate() && $result = MovieDateScraper::BookRestaurant(
                'zeke',
                'coys',
                $this->model->getRootPages()['dinner'],
                $this->model->getRootPage(),
                $this->view->getDate()
            )){
                $this->output = $result;
                $this->model->reset();
            }else{
                if(!$this->model->hasRootPages()){
                    $this->model->setRootPages(MovieDateScraper::findRootPages($this->model->getRootPage()));
                }

                $pages = $this->model->getRootPages();

                foreach($this->model->getRootPages() as $url){
                    if(!$this->model->hasCalendar()){
                        $this->model->setCalendars(MovieDateScraper::scanCalendar($pages['calendar']));
                    }elseif(!$this->model->hasCinema()){
                        $this->model->setCinema(MovieDateScraper::scanCinema($pages['cinema'], $this->model->getFreeDates()));
                    }elseif(!$this->model->hasDinner()){
                        $this->model->setDinner(MovieDateScraper::scanRestaurant($pages['dinner']));
                    }
                }
                $this->output = $this->view->showAvailable();
            }
        }
    }

    public function getView(){
        return $this->output;
    }







}