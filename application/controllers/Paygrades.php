<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Paygrades extends REST_Controller {

    function __construct() {

        parent::__construct();
        $this->load->model('paygrades_model');
    }

    public function index_get() {
        //Get params
        $company_id = (int) $this->get('company_id');

        log_message("debug", "*********** index_get start company_id {$company_id} ***********");

        if (!empty($company_id)) {
            $result = $this->paygrades_model->get_all_paygrades($company_id);

            $this->response([
                'response' => $result,
                'status' => TRUE,
                'description' => 'To get all [/paygrades/company_id/] or to get single [/paygrades/company_id/paygrade_id]'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No company_id was suplied',
                'description' => 'To get all [/paygrades/company_id/] or to get single [/paygrades/company_id/paygrade_id]'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put() {

        log_message("debug", "*********** index_put start ***********");

        $data = array(
            'company_id' => $this->put('company_id'),
            'pay_grade_name' => $this->put('pay_grade_name'),
        );

        log_message("debug", "Getting ready to insert... " . json_encode($data));

        if (empty($data['pay_grade_name'])) {

            log_message("debug", "index_put Trying to insert empty paygrade name... ");

            return $this->response([
                        'status' => FALSE,
                        'message' => 'Trying to create empty paygrade name',
                        'description' => 'create paygrade put/ {company_id,paygrade_name} name cannot be null'
                            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($this->paygrades_model->paygrade_exists($data['pay_grade_name'], $data['company_id']) == TRUE) {

            log_message("debug", "index_put Trying to duplicate a paygrade name... ");

            return $this->response([
                        'response' => $data,
                        'status' => FALSE,
                        'message' => 'Trying to duplicate a paygrade name',
                        'description' => 'create paygrade put/ {company_id,paygrade_name} name cannot be null'
                            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $response = $this->paygrades_model->create_paygrade($data);

        if ($response == FALSE) {

            log_message("debug", "index_put Database refused. Try again!... ");

            return $this->response([
                        'response' => $data,
                        'status' => FALSE,
                        'message' => 'Database refused. Try again!',
                        'description' => 'create paygrade put/ {company_id,paygrade_name} name cannot be null'
                            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        log_message("debug", "index_put Record created!... ");

        return $this->response([
                    'response' => $response,
                    'status' => true,
                    'message' => 'Department created!',
                    'description' => 'To get all [/paygrades/company_id/] or to get single [/paygrades/company_id/paygrade_id]'
                        ], REST_Controller::HTTP_CREATED);
    }

    public function find_get() {

        $paygrade_id = (int) $this->get('pay_grade_id');

        log_message("debug", "*********** find_get start paygrade_id {$paygrade_id} ***********");

        if (empty($paygrade_id)) {

            return $this->response([
                        'status' => FALSE,
                        'message' => 'No paygrade_id was suplied',
                        'description' => 'To get all [/paygrades/company_id/] or to get single [/paygrades/find/paygrade_id]'
                            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $result = $this->paygrades_model->get_single_paygrade("", $paygrade_id);

        $this->response([
            'response' => $result,
            'status' => TRUE,
            'description' => 'To get all [/paygrades/company_id/] or to get single [/paygrades/find/paygrade_id]'
                ], REST_Controller::HTTP_OK);
    }

    public function index_post() {

        $data = [
            'pay_grade_id' => $this->post('pay_grade_id'), // Automatically generated by the model
            'pay_grade_name' => $this->post('pay_grade_name'),
        ];

        if (empty($data['pay_grade_id']) || empty($data['pay_grade_name'])) {

            log_message("debug", "index_post Empty pay_grade_id/pay_grade_name supplied... ");

            return $this->response([
                        'response' => $data,
                        'status' => FALSE,
                        'message' => 'Empty pay_grade_id/pay_grade_name supplied',
                        'description' => 'Update paygrade post/ {pay_grade_id,pay_grade_name} name and id cannot be null'
                            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($this->paygrades_model->paygrade_id_exists($data['pay_grade_id']) != TRUE) {

            log_message("debug", "index_POST Record does not exist... ");

            return $this->response([
                        'response' => $data,
                        'status' => FALSE,
                        'message' => 'This paygrade you are trying to update does not exist',
                        'description' => 'Update paygrade post/ {pay_grade_id,pay_grade_name} name and id cannot be null'
                            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $response = $this->paygrades_model->update_paygrade($data);

        if ($response == FALSE) {

            return $this->response([
                        'response' => $data,
                        'status' => FALSE,
                        'message' => 'Database refused. Try again!',
                        'description' => 'Update paygrade post/ {pay_grade_id,pay_grade_name} name and id cannot be null'
                            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        log_message("debug", "Paygrade Updated...");

        return $this->response([
                    'response' => $response,
                    'status' => TRUE,
                    'message' => 'Paygrade Updated!',
                    'description' => 'To get all [/paygrades/company_id/] or to get single [/paygrades/company_id/pay_grade_id]'
                        ], REST_Controller::HTTP_OK);
    }

}