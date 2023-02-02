<?php

class InventoryTraceController extends MainController {

    public function index() {
        $trace = new InventoryTraceModel();
        $data = $trace->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function transferStock() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $inventory = new InventoryModel();
                    $trace = new InventoryTraceModel();
                    if ($data['transfer_type_id'] == 2) {
                        $status['inventory_update'] = $inventory->decrementStock($data);
                        if($status['inventory_update']['status'] == 1) {
                            $status['inventory_update'] = $inventory->incrementStock($data);
                            $status = $trace->transferStock($data);
                        }
                    }
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function returnStock() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $trace = new InventoryTraceModel();
                    if ($data['transfer_type_id'] == 3) {
                        if ($trace->supplierChecker($data)) {
                            $result = $trace->returnStock($data);
                        } else {
                            $result['code'] = 0;
                            $result['message'] = "This product has not been bought from the given supplier ID";
                        }
                    } else {
                        $result['code'] = 0;
                        $result['message'] = "Transfer type is not valid";
                    }
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
            }
        }
    }
}