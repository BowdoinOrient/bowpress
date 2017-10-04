<?php
class Series extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/New_York');
        $this->load->model('series_model','',TRUE);
        $this->load->model('article_model', '', TRUE);
        $this->load->model('attachments_model', '', TRUE);
        $this->load->model('author_model', '', TRUE);
    }
    
    public function index($id = '')
    {
        if(!$id || $id=='0') 
        {
            header("Location: http://bowdoinorient.com/404");
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
        $series = $this->series_model->get_series($id);
        
        if(!$series || $id==0) 
        {
            header("Location: http://bowdoinorient.com/404");
            die();
        }
        else
        {
            $data->footerdata->quote = $this->attachments_model->get_random_quote();
            $data->headerdata->date = date("Y-m-d");
            $data->series = $series;
            $data->articles = $this->article_model->get_articles_by_date(date("Y-m-d"), false, false, false, false, false, $id);
            $data->contributors = $this->author_model->get_series_contributors($id);
            
            // meta
            $data->page_title = $series->name." — The Bowdoin Orient";
            $data->page_description = htmlspecialchars(strip_tags($series->description));
            $data->page_type = 'website';
            if($series->photo) $data->page_image = base_url().'images/series/'.$series->photo;
            
            $this->load->view('series', $data);
        }
    }
    
}
?>