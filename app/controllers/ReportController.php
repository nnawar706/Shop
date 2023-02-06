<?php

class ReportController extends MainController {

    public function getCustomerDueReport($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->customerDues($data, $params['id']);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    public function getPurchaseReport($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->getPurchases($data, $params['id']);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    public function getProductSalesReport($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->getProductSales($data, $params['id']);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    public function getPerformanceReport($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->getPerformance($data, $params['id']);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    public function getSupplierSalesReport($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->getSupplierSales($data, $params['id']);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }

    public function getSupplierDueReport($f3, $params) {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $report = new ReportModel();
                    $info = $report->supplierDues($data, $params['id']);
                    header('Content-Type: application/json');
                    echo json_encode($info);
                }
            }
        }
    }
}