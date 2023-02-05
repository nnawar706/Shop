<?php

class SalesStatusController extends MainController
{

    public function index() {
        $status = new SalesStatusModel();
        $data = $status->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

}