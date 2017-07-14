<?php
class Pages_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_content($page){
        $this->db->where('page', $page); 
        $query = $this->db->get("pages");
        $row = $query->row();

        if($row){
            return $row->content;
        } else {
            return "";
        }
    }
}
?>