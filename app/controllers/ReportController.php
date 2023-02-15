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

    public function getAllPurchaseReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getAllPurchases($data);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getProductSalesReport($f3, $params) {
        $report = new ReportModel();
        $data['branch_id'] = $params['branch_id'];
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getProductSales($data);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
    }

    public function getAllProductSalesReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getAllProductSales($data);
        header('Content-Type: application/json');
        echo json_encode($info);
        $this->f3->status(200);
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

    public function getAllPerformanceReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getAllPerformance($data);
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

    public function getAllSupplierSalesReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->getAllSupplierSales($data);
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

    public function getAllSupplierDueReport($f3, $params) {
        $report = new ReportModel();
        $data['from'] = $params['from'];
        $data['to'] = $params['to'];
        $info = $report->allSupplierDues($data);
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