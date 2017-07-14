<?php
class Author extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/New_York');
        $this->load->model('author_model','',TRUE);
        $this->load->model('article_model', '', TRUE);
        $this->load->model('attachments_model', '', TRUE);
    }
    
    public function index($id = '')
    {
        if(!$id) 
        {
            die();
        }
        else
        {
            $this->view($id);
        }
    }
    
    public function error($message = '')
    {
        $data->message = $message;
        $this->load->view('error', $data);
    }
        
    public function view($id)
    {
        $data = new stdClass;
        $data->headerdata = new stdClass;
        $data->footerdata = new stdClass;

        $author = $this->author_model->get_author($id);
        
        if(!$author) 
        {
            header("Location: http://bowdoinorient.com/404");
            die();
        }
        else
        {
            $data->footerdata->quote = $this->attachments_model->get_random_quote();
            $data->headerdata->date = date("Y-m-d");
            $data->author = $author;
            
            $author_collaborators = $this->author_model->get_author_collaborators($id);
            $photo_collaborators = $this->author_model->get_photographer_collaborators($id);
            if ($author_collaborators && $photo_collaborators)
            {
                $data->collaborators = (object) array_merge((array) $author_collaborators, (array) $photo_collaborators);
            }
            elseif ($author_collaborators)
            {
                $data->collaborators = $author_collaborators;
            }
            elseif ($photo_collaborators)
            {
                $data->collaborators = $photo_collaborators;
            }


            $data->articles = $this->article_model->get_articles_by_date(date("Y-m-d"), false, false, false, false, $id);
            $data->popular = $this->article_model->get_popular_articles_by_date(date("Y-m-d"), false, '5',   false, $id, false);
            $data->series = $this->author_model->get_author_series($id);
            $data->longreads = $this->author_model->get_author_longreads($id);
            $data->stats = $this->author_model->get_author_stats($id);
            $data->photos = $this->attachments_model->get_author_photos($id);
            
            // meta
            $data->page_title = $author->name." — The Bowdoin Orient";
            $data->page_description = htmlspecialchars(strip_tags($author->bio));
            $data->page_type = 'profile';
            if($author->photo) $data->page_image = base_url().'images/authors/'.$author->photo;
            
            $this->load->view('author', $data);
        }
    }
    
}
?>