<?php
class Issue_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_issue($vol, $no)
    {
        $this->db->where('volume', $vol);
        $this->db->where('issue_number', $no);
        $query = $this->db->get('issue');   
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function get_latest_issue($date = false)
    {
        if($date) $this->db->where('issue_date <=', $date);
        $this->db->order_by('volume', 'desc');
        $this->db->order_by('issue_number', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('issue');
        return $query->row();
    }
    
    /**
     * get the next or previous issue. 
     * $dif should be +1 to get next, -1 to get previous.
     * it can't do bigger jumps.
     **/
    function get_adjacent_issue($vol, $no, $dif)
    {
        // for ordering next/prev vol later
        $dir = ($dif > 0 ? "asc" : "desc");
        
        // try next/prev issue in same vol
        $this->db->where("volume", $vol);
        $this->db->where("issue_number", $no+$dif);
        $this->db->where("ready", "1");
        $query = $this->db->get("issue");
        if($query->num_rows() > 0)
        {
            $adjissue = $query->row();
        }
        else
        {
            // try first/last issue in next/prev vol
            $this->db->where("volume", $vol+$dif);
            $this->db->where("ready", "1");
            $this->db->order_by("issue_number", $dir);
            $query = $this->db->get("issue");
            if($query->num_rows() > 0)
            {
                $adjissue = $query->row();
            }
            else
            {
                return false;
            }
        }
        return $adjissue;
    }
    
    function get_sections()
    {
        $this->db->order_by('priority', 'asc');
        $query = $this->db->get('section');
        return $query->result();
    }

}
?>