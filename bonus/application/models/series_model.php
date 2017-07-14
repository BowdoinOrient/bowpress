<?php
class Series_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_series($id)
    {
        $this->db->where("id", $id);
        
        // "active" basically means "hasn't been deleted". we should almost never show inactive articles.
        $this->db->where("active", "1");
        
        $query = $this->db->get("series");
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function get_all_series_ids()
    {
        $this->db->select('id');
        $this->db->where('active','1');
        $query = $this->db->get('series');
        return $query->result();
    }
    
    function get_series_by_name($series_name, $loose=false)
    {
        if(!$loose) $this->db->where('name', $series_name);
        if($loose) $this->db->like('name', $series_name);
        
        $query = $this->db->get('series');
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function add_series($name, $photo='', $description='')
    {
        $data = array(
           'name' => $name,
           'photo' => $photo,
           'description' => $description
        );
        return $this->db->insert('series', $data);
    }

}
?>