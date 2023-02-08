<?php

class PurchaseStatusController extends MainController {

    public function index() {
        $status = new PurchaseStatusModel();
        $data = $status->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }
}