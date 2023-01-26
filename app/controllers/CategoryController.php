<?php

class CategoryController extends MainController {

    public function index() {
        $category = new CategoryModel();
        $data = $category->getAll();
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
                    $category = new CategoryModel();
                    $status = $category->createCategory($data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function read($f3, $params) {
        $category = new CategoryModel();
        $data = $category->getCategory($params['id']);
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
                    $category = new CategoryModel();
                    $status = $category->updateCategory($params['id'], $data);
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function getByParent($f3, $params) {
        $category = new CategoryModel();
        $data = $category->getAllCategory($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function delete($f3, $params) {
        $category = new CategoryModel();
        $status = $category->deleteCategory($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }
}