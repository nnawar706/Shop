<?php

class CronController extends MainController {

    public function checkAlert() {
        $inventory = new InventoryModel();
        $data = $inventory->alertChecker();
        if($data) {
            foreach ($data as $item) {
                $notification = new NotificationModel();
                $notification->addCron($item);
                $inventory->updateFlag($item['id'], 1);
            }
        }
    }
}