<?php

class Processes_model extends CI_Model
{

    private $workshop_db;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->workshop_db = $this->load->database('workshop', true);
    }

    public function get_all_processes($company_id = '0')
    {

        $select_query = "SELECT
						`process`.`process_id`
						, `process`.`company_id`
						, `process`.`sequence`
						, `process`.`process_name`
						, `process`.`vehicle_make`
						, `make`.`description_name` AS vehicle_make_name
						, `process`.`vehicle_model`
						, `model`.`description_name` AS vehicle_model_name
						, `process`.`std_days`
						, `process`.`amount`
						, `process`.`critical_path`
						, `process`.`date_created`
					FROM
						`workshop`.`process`
						INNER JOIN `workshop`.`parameter_description` AS `make` 
							ON (`process`.`vehicle_make` = `make`.`description_id`)
						INNER JOIN `workshop`.`parameter_description` AS `model`
							ON (`process`.`vehicle_model` = `model`.`description_id`)
							WHERE `process`.`company_id` IN (?,?) ORDER BY `process`.`date_created` DESC;";

        if ($query = $this->workshop_db->query($select_query, array($company_id, '0'))) {

            log_message("debug", $this->db->last_query());

            log_message("debug", "found processes..." . json_encode($query->result()));

            return $query->result();
        } else {

            log_message("error", 'Error getting processes.');

            return false;
        }
    }

    public function get_single_process($company_id = '0', $process_id)
    {

        if (!empty($process_id)) {

            $select_query = "SELECT * FROM `process`
                        WHERE `process_id` = {$process_id};";

            "SELECT
						`process`.`process_id`
						, `process`.`company_id`
						, `process`.`sequence`
						, `process`.`process_name`
						, `process`.`vehicle_make`
						, `make`.`description_name` AS vehicle_make_name
						, `process`.`vehicle_model`
						, `model`.`description_name` AS vehicle_model_name
						, `process`.`std_days`
						, `process`.`amount`
						, `process`.`critical_path`
						, `process`.`date_created`
					FROM
						`workshop`.`process`
						INNER JOIN `workshop`.`parameter_description` AS `make` 
							ON (`process`.`vehicle_make` = `make`.`description_id`)
						INNER JOIN `workshop`.`parameter_description` AS `model`
							ON (`process`.`vehicle_model` = `model`.`description_id`)
							WHERE `process`.`process_id` = {$process_id};";

            if ($query = $this->workshop_db->query($select_query)) {

                log_message("debug", $this->db->last_query());

                log_message("debug", "found process..." . json_encode($query->result()));

                return $query->result();
            } else {

                log_message("error", 'Error getting process.');

                return false;
            }
        } else {
            //The department_id was empty
            return FALSE;
        }
    }

    public function create_processes($data)
    {
        $data['critical_path'] == null ? "0" : "1";

        if ($this->workshop_db->insert('process', $data)) {

            log_message("debug", "create_processes query " . $this->workshop_db->last_query());

            $id = $this->workshop_db->insert_id();

            $new_record = $this->workshop_db->get_where('process', array('process_id' => $id));

            log_message("debug", " process created " . json_encode($new_record->row()));

            return $new_record->row();
        } else {
            return FALSE;
        }
    }

    public function process_name_exists($process_name, $vehicle_make, $vehicle_model, $company_id)
    {

        $this->workshop_db->where('process_name', $process_name);

        $this->workshop_db->where('company_id', $company_id);

        $this->workshop_db->where('vehicle_make', $vehicle_make);

        $this->workshop_db->where('vehicle_model', $vehicle_model);

        $query = $this->workshop_db->get('process');

        log_message("debug", "process_name_exists " . $this->workshop_db->last_query());

        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function process_sequence_exists($sequence, $vehicle_make, $vehicle_model, $company_id)
    {

        $this->workshop_db->where('sequence', $sequence);

        $this->workshop_db->where('company_id', $company_id);

        $this->workshop_db->where('vehicle_make', $vehicle_make);

        $this->workshop_db->where('vehicle_model', $vehicle_model);

        $query = $this->workshop_db->get('process');

        if ($query->num_rows() >= 1) {

            return true;
        } else {

            return false;
        }
    }

    public function update_process($data)
    {

        if (empty($data['process_id'])) {

            log_message("debug", " process_id was empty. Exit");

            return FALSE;
        }
        $data['critical_path'] == null ? "0" : "1";

        $this->workshop_db->where('process_id', $data['process_id']);

        if ($this->workshop_db->update('process', $data) == FALSE) {

            return FALSE;
        }
        log_message("debug", "update_process " . $this->workshop_db->last_query());
        //All went well
        $new_record = $this->workshop_db->get_where('process', array('process_id' => $data['process_id']));

        log_message("debug", " update_process query " . $this->workshop_db->last_query());

        log_message("debug", " process Updated " . json_encode($new_record->row()));

        return $new_record->row();
    }

    public function process_id_exists($process_id)
    {
        $this->workshop_db->where('process_id', $process_id);

        $query = $this->workshop_db->get('process');

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

}
