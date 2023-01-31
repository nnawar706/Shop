<?php

class PurchaseProductController extends MainController {

    public function index() {
        $purchase = new PurchaseProductModel();
        $data = $purchase->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

}