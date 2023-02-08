<?php

class InventoryController extends MainController {

    public function index() {
        $inventory = new InventoryModel();
        $data = $inventory->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    public function branchWiseRead($f3, $params) {
        $inventory = new InventoryModel();
        $data = $inventory->getByBranch($params['branch_id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        $this->f3->status(200);
    }

    /**
     * @throws Exception
     */
    public function create() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $inventory = new InventoryModel();
                    $status = $inventory->createInventory($data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(201);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function update() {
        if ($this->f3->VERB == 'PUT' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $inventory = new InventoryModel();
                    $status = $inventory->updateMinStockAlert($data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                    $this->f3->status(201);
                }
            }
        }
    }
}