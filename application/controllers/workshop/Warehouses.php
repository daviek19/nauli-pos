<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Warehouses extends REST_Controller
{

    function __construct()
    {

        parent::__construct();
        $this->load->model('workshop/warehouse_model');
    }

    public function index_get()
    {
        //Get params
        $company_id = (int)$this->get('company_id');

        log_message("debug", "*********** index_get start company_id {$company_id} ***********");

        $result = $this->warehouse_model->get_all_warehouses($company_id);

        $this->response([
            'response' => $result,
            'status' => TRUE,
            'description' => 'To get all [/workshop/Warehouses/company_id/] or to get single [/workshop/find/company_id/wh_id]'
        ], REST_Controller::HTTP_OK);

    }

    public function find_get()
    {
        $wh_id = (int)$this->get('wh_id');

        log_message("debug", "*********** find_get start group_id {$wh_id} ***********");

        $result = $this->warehouse_model->get_single_warehouse("", $wh_id);

        $this->response([
            'response' => $result,
            'status' => TRUE,
            'description' => 'To get all [/workshop/Warehouses/company_id/] or to get single [/workshop/find/company_id/wh_id]'
        ], REST_Controller::HTTP_OK);
    }

    public function index_put()
    {

        log_message("debug", "*********** index_put start ***********");

        $data = array(
            'company_id' => $this->put('company_id'),
            'wh_name' => $this->put('wh_name'),
            'wh_loc' => $this->put('wh_loc')
        );

        log_message("debug", "Getting ready to insert... " . json_encode($data));

        if (empty($data['wh_name'])) {

            log_message("debug", "index_put Trying to insert empty warehouse name... ");

            return $this->response([
                'status' => FALSE,
                'message' => 'Trying to create empty warehouse name',
                'description' => 'create group put/ {company_id,warehouse_name,wh_loc_id} name cannot be null'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if (empty($data['wh_loc'])) {

            log_message("debug", "index_put Trying to insert empty location... ");

            return $this->response([
                'status' => FALSE,
                'message' => 'Trying to create with empty location',
                'description' => 'create group put/ {company_id,warehouse_name,wh_loc_id} name cannot be null'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($this->warehouse_model->warehouse_exists($data['wh_name'], $data['company_id']) == TRUE) {

            log_message("debug", "index_put Trying to duplicate a warehouse name... ");

            return $this->response([
                'response' => $data,
                'status' => FALSE,
                'message' => 'Trying to duplicate a warehouse name',
                'description' => 'create group put/ {company_id,warehouse_name,wh_loc_id} name cannot be null'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $response = $this->warehouse_model->create_warehouse($data);

        if ($response == FALSE) {

            log_message("debug", "index_put Database refused. Try again!... ");

            return $this->response([
                'response' => $data,
                'status' => FALSE,
                'message' => 'Database refused. Try again!',
                'description' => 'create group put/ {company_id,warehouse_name,wh_loc_id} name cannot be null'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        log_message("debug", "index_put Record created!... ");

        return $this->response([
            'response' => $response,
            'status' => true,
            'message' => 'Warehouse created!',
            'description' => 'create group put/ {company_id,warehouse_name,wh_loc_id} name cannot be null'
        ], REST_Controller::HTTP_CREATED);
    }

    public function index_post()
    {
        $data = [
            'wh_id' => $this->post('wh_id'), // Automatically generated by the model
            'wh_name' => $this->post('wh_name'),
            'wh_loc' => $this->post('wh_loc')
        ];

        if (empty($data['wh_id']) || empty($data['wh_name']) || empty($data['wh_loc'])) {

            log_message("debug", "index_post Empty warehouse id/warehouse name/location supplied... ");

            return $this->response([
                'response' => $data,
                'status' => FALSE,
                'message' => 'Empty warehouse id/warehouse name/location supplied',
                'description' => 'Update warehouse post/ {wh_id,wh_name,wh_loc} name and id cannot be null'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($this->warehouse_model->warehouse_id_exists($data['wh_id']) != TRUE) {

            log_message("debug", "index_POST Record does not exist... ");

            return $this->response([
                'response' => $data,
                'status' => FALSE,
                'message' => 'This warehouse you are trying to update does not exist',
                'description' => 'Update group post/ {wh_id,wh_name,wh_loc} name and id cannot be null'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $response = $this->warehouse_model->update_warehouse($data);

        if ($response == FALSE) {

            return $this->response([
                'response' => $data,
                'status' => FALSE,
                'message' => 'Database refused. Try again!',
                'description' => 'Update warehouse post/ {wh_id,wh_name,wh_loc} name and id cannot be null'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        log_message("debug", "warehouse Updated...");

        return $this->response([
            'response' => $response,
            'status' => TRUE,
            'message' => 'Warehouse Updated!',
            'description' => 'Update warehouse post/ {wh_id,wh_name,wh_loc} name and id cannot be null'
        ], REST_Controller::HTTP_OK);

    }
}