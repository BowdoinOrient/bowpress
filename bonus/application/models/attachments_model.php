<?php
class Attachments_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->model('article_model', '', TRUE);
    }
    
    ////////////
    // PHOTOS //
    ////////////
    
    function get_photos($article_id, $thumbnails_only = 0)
    {
        $this->db->select("
            photo.id as photo_id, 
            photo.filename_small, 
            photo.filename_large, 
            photo.credit, 
            photo.caption,
            photo.coverphoto,
            photo.afterpar,
            author.id as photographer_id, 
            author.name as photographer_name");
        $this->db->join("author", "author.id = photo.photographer_id", 'left');
        $this->db->from("photo");
        $this->db->where("article_id", $article_id);
        $this->db->where("photo.active", "1");
        if($thumbnails_only == 1)
            $this->db->where("photo.thumbnail_only", 0);
        $this->db->order_by("priority", "asc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_author_photos($author_id)
    {
        $this->db->select("
            photo.id as photo_id, 
            photo.filename_small, 
            photo.filename_large, 
            photo.credit, 
            photo.caption, 
            photo.article_id,
            article.title,
            article.date");
        $this->db->join("article", "article.id = photo.article_id", 'left');
        $this->db->from("photo");
        $this->db->where("photographer_id", $author_id);
        $this->db->where("photo.active", "1");
        $this->db->where("article.active", "1");
        $this->db->where("article.published", "1");
        $this->db->order_by("article.date", "desc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_coverphoto($article_id)
    {
        $this->db->select("
            photo.id as photo_id,
            photo.filename_large,
            photo.credit,
            photo.caption,
            author.id as photographer_id,
            author.name as photographer_name
            ");
        $this->db->join("author", "author.id = photo.photographer_id", 'left');
        $this->db->from("photo");
        $this->db->where("photo.article_id", $article_id);
        $this->db->where("photo.coverphoto", "1");
        $this->db->where("photo.active", "1");
        $this->db->order_by("photo.priority", "asc");
        $query = $this->db->get();
        if($query->num_rows() > 0){
            return current($query->result());
        } else {
            return FALSE;
        }
    }


    
    function add_photo($filename_small, $filename_large, $filename_original, $credit, $caption, $article_id, $priority='1', $hidephoto)
    {
        $this->load->model('author_model', '', TRUE);

        $hidephoto=="true" ? $thumbonly = 1 : $thumbonly = 0;

        $photographer_id = null;
        // if it appears a photographer has been entered
        if(strlen($credit) > 1) {
            $photographer = $this->author_model->get_author_by_name($credit);
            if(!$photographer)
            {
                $this->author_model->add_author($credit);
                $photographer = $this->author_model->get_author_by_name($credit);
            }
            $photographer_id = $photographer->id;
        }
        
        //         $author     = trim(preg_replace('/\&nbsp\;/', ' ',strip_tags(urldecode($this->input->post("author")))));
        //        if(strlen($author) > 1 && strlen($authorjob) > 1)    THEN ADD_ARTICLE_AUTHOR
        //        if(!$author)                                         THEN ADD AUTHOR 
        
        $data = array(
           'filename_small'     => $filename_small,
           'filename_large'     => $filename_large,
           'filename_original'     => $filename_original,
           'photographer_id'     => $photographer_id,
           'caption'             => $caption,
           'article_id'         => $article_id,
           'priority'             => $priority,
           'thumbnail_only'     => $thumbonly
        );
        return $this->db->insert('photo', $data);
    }
    
    function edit_photo($photo_id, $credit, $caption)
    {
        $this->load->model('author_model', '', TRUE);
        
        $credit = trim(str_replace("&nbsp;", ' ', $credit));
        if(empty($credit) || !$credit || $credit == '&nbsp;')
        {
            $photographer_id = '';
        }
        else
        {
            $photographer = $this->author_model->get_author_by_name($credit);
            if(!$photographer)
            {
                $this->author_model->add_author($credit);
                $photographer = $this->author_model->get_author_by_name($credit);
            }
            $photographer_id = $photographer->id;
        }
        
        $data = array(
            'photographer_id'    => $photographer_id,
            'caption'            => $caption
        );
        $this->db->where('id', $photo_id);
        return $this->db->update('photo', $data);
    }
    
    function delete_photo($photo_id)
    {
        $this->db->set('active', '0');
        $this->db->where('id', $photo_id);
        return $this->db->update('photo');
    }
            
    function get_random_quote($filter = TRUE, $public = '1')
    {
        $this->db->order_by('id', 'random');
        if($filter) $this->db->where('public', $public);
        $query = $this->db->get('quote');
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function count_article_photos($id)
    {
        $this->db->select("count(*) as count");
        $this->db->where("article_id", $id);
        $query = $this->db->get("photo");
        $result = $query->row();
        return $result->count;
    }
    
    ///////////////////////////
    // NON-PHOTO ATTACHMENTS //
    ///////////////////////////
    
    function get_attachment($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('attachments');
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function add_attachment($data)
    {
        $this->db->insert('attachments', $data);
        return $this->db->insert_id();
    }
    
    function get_attachments($article_id)
    {
        $this->db->select("
            attachments.*, 
            author.name as author_name");
        $this->db->join("author", "author.id = attachments.author_id", 'left');
        $this->db->from("attachments");
        $this->db->where("attachments.article_id", $article_id);
        $this->db->where("attachments.active", "1");
        //$this->db->order_by("priority", "asc");
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
        else
        {
            return FALSE;
        }
    }
    
    function set_big($attachment_id, $big)
    {
        $big_value = ($big=='true' ? '1' : '0');
        $this->db->set('big', $big_value);
        $this->db->where('id', $attachment_id);
        return $this->db->update('attachments');
    }
    
    function delete_attachment($attachment_id)
    {
        $this->db->set('active', '0');
        $this->db->where('id', $attachment_id);
        return $this->db->update('attachments');
    }
    
    // delete youtube 'playlist', aka every youtube attachment for an article
    function delete_attachment_playlist($article_id)
    {
        $this->db->set('active', '0');
        $this->db->where('type', 'youtube');
        $this->db->where('article_id', $article_id);
        return $this->db->update('attachments');
    }    
    
    // notice that this is identical to edit_photo
    // bad form. #dry (that said, i'm not quite ready to combine photos and attachments)
    function edit_attachment($attachment_id, $credit, $caption)
    {
        $this->load->model('author_model', '', TRUE);
        
        $credit = trim(str_replace("&nbsp;", ' ', $credit));
        if(empty($credit) || !$credit || $credit == '&nbsp;')
        {
            $author_id = '';
        }
        else
        {
            $author = $this->author_model->get_author_by_name($credit);
            if(!$author)
            {
                $this->author_model->add_author($credit);
                $author = $this->author_model->get_author_by_name($credit);
            }
            $author_id = $author->id;
        }
        
        $data = array(
            'author_id'    => $author_id,
            'content2'    => $caption
        );
        $this->db->where('id', $attachment_id);
        return $this->db->update('attachments', $data);
    }
        
}
?>