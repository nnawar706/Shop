<?php

class UserDesignationController extends MainController
{
    public function getSalesman() {
        $des = new UserDesignationModel();
        $data = $des->getSalesman();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

}