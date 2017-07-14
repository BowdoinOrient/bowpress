<?php
class Ad_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_ads(){
        $ads = $this->db->get('ads')->result();

        return $ads;
    }

    function get_current_ads(){
        $today = date("Y-m-d");

        $this->db->where('DATE_FORMAT(ads.start_date,"%Y-%m-%d") <=', $today);
        $this->db->where('DATE_FORMAT(ads.end_date,"%Y-%m-%d") >=', $today);

        $ads = $this->db->get('ads')->result();

        return $ads;
    }

    function get_ad(){
        $ads = $this->get_current_ads();

        if($ads != null) {
            $ad = $ads[array_rand($ads)];
            return $ad;
        } else {
            return null;
        }
    }

    function delete_ad($ad_id){
        $this->db->where('id', $ad_id);

        // CI's delete syntax makes me anxious - it looks like I'm telling it to delete
        // a whole table and I'm worried that one day it will actually just do that
        $this->db->delete('ads');
    }

    function create_ad($data){
        $this->db->insert('ads', $data);
    }

    function count_ads(){
        return $this->db->count_all_results('ads');
    }
}
?>