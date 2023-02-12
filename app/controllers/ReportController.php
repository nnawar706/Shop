<?php

class ReportController extends MainController {

    public function getCustomerDueReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->customerDues($data, $params['id']);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getAllCustomerDueReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->allCustomerDues($data);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getPurchaseReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getPurchases($data, $params['id']);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getProductSalesReport() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->getProductSales($data);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                    $this->f3->status(201);
                }
            }
        }
    }

    public function getPerformanceReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getPerformance($data, $params['id']);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getSupplierSalesReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getSupplierSales($data, $params['id']);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getSupplierDueReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->supplierDues($data, $params['id']);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getCategoryWiseSalesReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->categoryWiseProduct($data);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getRevenueReport($f3, $params) {
        $report = new ReportModel();
        $info = $report->revenue($params['id']);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }
}