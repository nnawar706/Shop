<?php

class CronController extends MainController {

    public function checkAlert() {
        $inventory = new InventoryModel();
        $data = $inventory->alertChecker();
        if($data) {
            $log = new LogModel();
            $log->addCron($data);
        }
    }
}