<?php
class API_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    //does some heavy lifting for both the JSON and XML APIs
    //I don't know or particularly care if this is good MVC or not
    function articlelist($issue_date, $section)
    {
        $data->issue_date = $issue_date;
        $data->section = $section;
        
        $this->db->select("
            date_format(issue.issue_date, '%b %e, %Y') as date,
            issue.issue_number,
            volume.arabic as volume", false);
        $this->db->join('volume', 'issue.volume = volume.arabic', 'inner');
        $this->db->where('issue.issue_date', $issue_date);
        $issue_query = $this->db->get('issue');
        $data->issue = $issue_query->row();
        
        
        $articles_query = $this->db->query("
            select
                article.id,
                article.priority,
                article.title,
                article.excerpt,
                author.name,
                photo.filename_small
            from article
            left join articleauthor on articleauthor.article_id = article.id
            left join author on author.id = articleauthor.author_id
            left join photo on photo.article_id = article.id
            where article.volume = '".$data->issue->volume."'
            and article.issue_number = '".$data->issue->issue_number."'
            and article.section_id = '".$data->section."'
            and article.published = '1'
            and article.active = '1'
            order by article.priority
        ");
        $data->articles = $articles_query->result();
        
        return $data; 
    }
    function fulltext($issue_date, $section)
    {
        $data->issue_date = $issue_date;
        $data->section = $section;
        
        $this->db->select("
            date_format(issue.issue_date, '%b %e, %Y') as date,
            issue.issue_number,
            volume.arabic as volume", false);
        $this->db->join('volume', 'issue.volume = volume.arabic', 'inner');
        $this->db->where('issue.issue_date', $issue_date);
        $issue_query = $this->db->get('issue');
        $data->issue = $issue_query->row();
        
        
        $articles_query = $this->db->query("
            select
                article.id,
                article.priority,
                article.title,
                author.name,
                articlebody.body
            from article
            left join articleauthor on articleauthor.article_id = article.id
            left join author on author.id = articleauthor.author_id
            left join articlebody on articlebody.id = article.id
            where article.volume = '".$data->issue->volume."'
            and article.issue_number = '".$data->issue->issue_number."'
            and article.section_id = '".$data->section."'
            and article.published = '1'
            and article.active = '1'
            order by article.priority
        ");
        $data->articles = $articles_query->result();
        
        return $data;
    }

}
?>