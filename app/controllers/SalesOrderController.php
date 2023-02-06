<?php

class SalesOrderController extends MainController {

    public function index($f3, $params) {
        $order = new SalesOrderModel();
        $data = $order->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
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
                    $user = new UserModel();
                    $data['branch_id'] = $user->getBranch($data['user_id']);
                    $inventory = new InventoryModel();
                    $eligibility = $inventory->checkEligibility($data);
                    if (!in_array("0", $eligibility['code'])) {
                        $order = new SalesOrderModel();
                        $status = $order->createOrder($data);
                    } else {
                        $status['status']['code'] = 0;
                        $status['status']['message'] = $eligibility['message'];
                    }
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function read($f3, $params) {
        $order = new SalesOrderModel();
        $data = $order->getSales($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function update($f3, $params) {
        if ($this->f3->VERB == 'PUT' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'),true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $order = new SalesOrderModel();
                    $status = $order->updateOrder($params['id'], $data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function delete($f3, $params) {
        $order = new SalesOrderModel();
        $status = $order->deleteOrder($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }
}