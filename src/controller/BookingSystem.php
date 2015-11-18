<?php

namespace controller;

class BookingSystem
{
    private $model;
    private $view;
    private $output;

    private $username;
    private $password;

    public function __construct($username, $password, $waiting = 0){

        $this->username = $username;
        $this->password = $password;

        $this->model = new \model\BookingSystem($waiting);
        $this->view = new \view\BookingSystem($this->model);

        $this->doControl();
    }

    public function doControl(){
        if($this->view->userReset()){
            $this->reset();
        }
        if($this->view->submittedRootPage()){
            $this->model->setRoot($this->view->getRoot());
        }

        if($this->model->hasRootPage() == false){
            $this->output = $this->view->showRootForm();
        }else{
            try{
                $this->model->scan();

                if($this->view->submittedDate() && $result = $this->model->bookRestaurant($this->username, $this->password, $this->view->getDate())){
                    $this->view->setSuccessfulBooking($result);
                    $this->reset();
                }else{
                    $this->output = $this->view->showAvailable();
                }
            }catch(\ScraperException $e){
                $this->model->reset();
                $this->output = $this->view->showRootForm($e->getMessage());
            }catch(\Exception $e){
                $this->model->reset();
                $this->output = $this->view->showRootForm("Unexpected Error");
            }
        }
    }

    private function reset(){
        $this->model->reset();
        $this->view->reset();
    }

    public function getView(){
        return $this->output;
    }
}