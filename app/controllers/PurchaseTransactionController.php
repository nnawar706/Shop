<?php

class PurchaseTransactionController extends MainController {

    public $doc_name;

    public function index() {
        $purchase = new PurchaseTransactionModel();
        $data = $purchase->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function uploadDoc() {
        $this->f3->set('UPLOADS','ui/images/suppliers/');
        $files = Web::instance()->receive(function($file,$formFieldName){
            return true;
        }, false, function($fileBaseName, $formFieldName){
            $file_data = pathinfo($fileBaseName);
            $this->doc_name = "transaction_" . time() . rand(100,999) . "." . $file_data['extension'];
            return $this->doc_name;
        });
    }

    /**
     * @throws Exception
     */
    public function create() {
        if($this->f3->exists('POST.create')) {
            $purchase = new PurchaseTransactionModel();
            $result = $purchase->createPurchase($this->f3->get('POST'));
            if($result['status']['code']===1){
                try {
                    $this->uploadDoc();
                    if($this->doc_name!=''){
                        $purchase->addDoc($result['data']['id'], 'https://nafisa.selopian.us/ui/images/suppliers/'.$this->doc_name);
                        $result['data']['transaction_document_url']= 'https://nafisa.selopian.us/ui/images/suppliers/'.$this->doc_name;
                    }
                } catch(PDOException $e) {
                    $purchase->deletePurchase($result['data']['id']);
                    $result['status']['code'] = 0;
                    $result['status']['message'] = "Sorry, couldn't add transaction info.";
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public function read($f3, $params) {
        $purchase = new PurchaseTransactionModel();
        $data = $purchase->getPurchase($params['id']);
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
                    $purchase = new PurchaseTransactionModel();
                    $status = $purchase->updatePurchase($params['id'], $data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function delete($f3, $params) {
        $purchase = new PurchaseTransactionModel();
        $status = $purchase->deletePurchase($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }
}