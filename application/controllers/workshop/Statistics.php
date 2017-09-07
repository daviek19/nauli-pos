<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Statistics extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('workshop/banks_model');
    }

    public function index_get()
    {
        //Get params
        $company_id = (int)$this->get('company_id');

        log_message("debug", "*********** index_get start company_id {$company_id} ***********");

        $result = $this->banks_model->get_all_banks($company_id);

        $this->response([
            'response' => $result,
            'status' => TRUE,
            'description' => 'To get all [/workshop/banks/company_id/] or to get single [/workshop/banks/company_id/item_id]'
        ], REST_Controller::HTTP_OK);

    }
}