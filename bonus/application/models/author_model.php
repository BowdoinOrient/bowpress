<?php
class Author_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_author($id)
    {
        $this->db->where("id", $id);
        
        // "active" basically means "hasn't been deleted". we should almost never show inactive articles.
        $this->db->where("active", "1");
        
        $query = $this->db->get("author");
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function get_authors()
    {
        $this->db->where('active','1');
        $query = $this->db->get('author');
        return $query->result();
    }
    
    function get_authors_array()
    {
        // for bonus merge tool dropdown. shouldn't be used publicly, esp due to
        // htmlentities encoding. 
        $this->db->where('active','1');
        $this->db->order_by('name','asc');
        $query = $this->db->get('author');
        $authors = $query->result();
        $authors_array = array();
        foreach($authors as $author)
        {
            $authors_array[$author->id] = htmlspecialchars($author->name);
        }
        return $authors_array;
    }
    
    function get_author_by_name($name, $loose=false)
    {
        if(!$name || empty($name)) return false;
        
        if(!$loose) $this->db->where('name', $name);
        if($loose) $this->db->like('name', $name);
        $query = $this->db->get('author');
        
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function add_author($name, $photo='', $job='0', $classyear='', $bio='')
    {
        $data = array(
           'name' => $name,
           'photo' => $photo,
           'job' => $job,
           'bio' => $bio
        );
        return $this->db->insert('author', $data);
    }
    
    function get_job_by_name($name)
    {
        if(!$name || empty($name)) return false;
        
        $this->db->where('name', $name);
        $query = $this->db->get('job');
        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function add_job($name)
    {
        $data = array(
           'name' => $name ,
        );
        return $this->db->insert('job', $data);
    }
    
    function get_author_series($id)
    {
        $this->db->select('title, series, name');
        $this->db->join('articleauthor', 'articleauthor.article_id = article.id');
        $this->db->join('series', 'series.id = article.series');
        $this->db->group_by('series');
        $this->db->where('articleauthor.author_id', $id);
        $this->db->where('name !=', '');
        $query = $this->db->get('article');
        return $query->result();
    }
    
    function get_author_longreads($id)
    {
        $this->db->select('article.id, article.title, length(body) as bodylength');
        $this->db->join('articlebody', 'articlebody.article_id = article.id');
        $this->db->join('articleauthor', 'articleauthor.article_id = article.id');
        $this->db->where('articleauthor.author_id', $id);
        //$this->db->order_by('articlebody.timestamp', 'desc');
        $this->db->order_by('bodylength', 'desc');
        $this->db->group_by('article.id');
        $this->db->limit('5');
        $query = $this->db->get('article');
        return $query->result();        
    }
    
    function get_author_collaborators($id)
    {
        $this->db->select('article_id');
        $this->db->where('author_id', $id);
        $query = $this->db->get('articleauthor');
        
        if($query->num_rows() > 0)
        {
            $article_ids_2d = $query->result();
        
            $article_ids = array();
            foreach($article_ids_2d as $aid) {
                $article_ids[] = $aid->article_id;
            }
        
            $this->db->select('author.id as author_id, author.name, article.id as article_id, article.title, count(*) as collab_count');
            $this->db->where_in('article_id', $article_ids);
            $this->db->where('articleauthor.author_id !=', $id);
            $this->db->join('author', 'author.id = articleauthor.author_id');
            $this->db->join('article', 'article.id = articleauthor.article_id');
            $this->db->group_by('author.id');
            $query2 = $this->db->get('articleauthor');
                
            return $query2->result();
        }
        else
        {
            return false;
        }
    }
    
    function get_photographer_collaborators($id)
    {
        $this->db->select('article_id');
        $this->db->where('photographer_id', $id);
        $this->db->where('active', '1');
        $query = $this->db->get('photo');
        
        if($query->num_rows() > 0)
        {
            $article_ids_2d = $query->result();
            
            $article_ids = array();
            foreach($article_ids_2d as $aid) {
                $article_ids[] = $aid->article_id;
            }
            
            $this->db->select('author.id as author_id, author.name, article.id as article_id, article.title, count(*) as collab_count');
            $this->db->where_in('article_id', $article_ids);
            $this->db->where('photo.photographer_id !=', $id);
            $this->db->where('photo.active', '1');
            $this->db->where('author.active', '1');
            $this->db->where('article.active', '1');
            $this->db->join('author', 'author.id = photo.photographer_id');
            $this->db->join('article', 'article.id = photo.article_id');
            $this->db->group_by('author.id');
            $query2 = $this->db->get('photo');
                
            return $query2->result();
        }
        else
        {
            return false;
        }
    }
    
    function get_author_stats($id)
    {
        $data = array();
        
        // count articles
        $this->db->select('count(*) as articlecount');
        $this->db->where('author_id', $id);
        $this->db->where('article.active', '1');
        $this->db->where('article.published', '1');
        $this->db->join('article', 'article.id=articleauthor.article_id');
        $query = $this->db->get('articleauthor');
        $result = $query->row();
        $data['article_count'] = $result->articlecount;
        
        // count photos
        $this->db->select('count(*) as photocount');
        $this->db->where('photographer_id', $id);
        $this->db->where('active', '1');
        $query = $this->db->get('photo');
        $result = $query->row();
        $data['photo_count'] = $result->photocount;
        
        // earliest article date
        $this->db->select('article.date_published');
        $this->db->join('article', 'article.id = articleauthor.article_id');
        $this->db->where('articleauthor.author_id', $id);
        $this->db->where('article.active', '1');
        $this->db->order_by('article.date_published', 'asc');
        $this->db->limit('1');
        $query = $this->db->get('articleauthor');
        if($query->num_rows() > 0) 
        {
            $result = $query->row();
            $data['first_article'] = $result->date_published;
        }
        else
        {
            $data['first_article'] = false;
        }
        
        // latest article date
        $this->db->select('article.date_published');
        $this->db->join('article', 'article.id = articleauthor.article_id');
        $this->db->where('articleauthor.author_id', $id);
        $this->db->where('article.active', '1');
        $this->db->order_by('article.date_published', 'desc');
        $this->db->limit('1');
        $query = $this->db->get('articleauthor');
        if($query->num_rows() > 0) 
        {
            $result = $query->row();
            $data['latest_article'] = $result->date_published;
        }
        else
        {
            $data['latest_article'] = false;
        }
        
        // earliest photo date
        $this->db->select('article.date_published');
        $this->db->join('article', 'article.id = photo.article_id');
        $this->db->where('photo.photographer_id', $id);
        $this->db->where('article.active', '1');
        $this->db->where('photo.active', '1');
        $this->db->order_by('article.date_published', 'asc');
        $this->db->limit('1');
        $query = $this->db->get('photo');
        if($query->num_rows() > 0) 
        {
            $result = $query->row();
            $data['first_photo'] = $result->date_published;
        }
        else
        {
            $data['first_photo'] = false;
        }
        
        // latest photo date
        $this->db->select('article.date_published');
        $this->db->join('article', 'article.id = photo.article_id');
        $this->db->where('photo.photographer_id', $id);
        $this->db->where('article.active', '1');
        $this->db->where('photo.active', '1');
        $this->db->order_by('article.date_published', 'desc');
        $this->db->limit('1');
        $query = $this->db->get('photo');
        if($query->num_rows() > 0) 
        {
            $result = $query->row();
            $data['latest_photo'] = $result->date_published;
        }
        else
        {
            $data['latest_photo'] = false;
        }
        
        /*
        
        author lifetime wordcount, which would be great, would look something like this:
        
        SELECT SUM( LENGTH(name) - LENGTH(REPLACE(name, ' ', ''))+1)
        FROM articlebody;
        http://www.mwasif.com/tag/mysql-word-count/

        SELECT articlebody.*, COUNT(*) AS count
           FROM articlebody
           JOIN (SELECT max(`timestamp`) AS `timestamp`
                      FROM articlebody 
                      GROUP by `timestamp`) P2 ON P2.timestamp = articlebody.timestamp
           GROUP BY articlebody.article_id
           ORDER BY articlebody.timestamp ASC 
           LIMIT 10;
        http://stackoverflow.com/questions/190702/mysql-select-n-rows-but-with-only-unique-values-in-one-column
        
        unfortunately, it's hella slow. f me for breaking bodies into a separate table. :-/
        maybe a little redundancy wouldn't be bad and i really should be saving the latest body in `article`.
        f it, imma do that. eventually.
        <3, toph
        
        UPDATE: wait this is totally doable, i had some breakthrough and then i lost it oops.
        
        */
        
        return $data;
    }
    
    function get_series_contributors($id)
    {
        $this->db->select('author.id as author_id, author.name, count(*) as contrib_count');
        $this->db->join('articleauthor', 'articleauthor.author_id = author.id');
        $this->db->join('article', 'article.id = articleauthor.article_id');
        $this->db->where('article.series', $id);
        $this->db->where('article.active', '1');
        $this->db->where('article.published', '1');
        $this->db->group_by('author_id');
        $this->db->order_by('contrib_count', 'desc');
        $query = $this->db->get('author');
        return $query->result();
    }

    function get_all_author_ids()
    {
        $this->db->select('id');
        $this->db->where('active','1');
        $query = $this->db->get('author');
        return $query->result();
    }
    
    function merge_authors($from_id, $to_id)
    {
        //change articles
        $this->db->where('author_id', $from_id);
        $this->db->set('author_id', $to_id);
        $query1 = $this->db->update('articleauthor');
        
        //change photos
        $this->db->where('photographer_id', $from_id);
        $this->db->set('photographer_id', $to_id);
        $query2 = $this->db->update('photo');
        
        //deactivate the from_ author
        $this->db->set('active', '0');
        $this->db->where('id', $from_id);
        $query3 = $this->db->update('author');
        
        return ($query1 && $query2 && $query3);
    }
    
}
?>