<?php $this->load->view('template/head'); ?>

<body>

<?php $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
            
    <header class="authorheader">
                
        <?php if(!empty($author->photo)): ?>
            <figure class="authorpic"><img src="<?php echo base_url().'images/authors/'.$author->photo?>"></figure>
        <?php endif; ?>
                
        <div class="authorstats">
            <h2 class="authorname"><?php echo $author->name?></h2>
            
            <?php if($stats['article_count']): ?><strong>Number of articles:</strong> <?php echo  $stats['article_count'] ?><br/><?php endif; ?>
            <?php if($stats['photo_count']): ?><strong>Number of photos:</strong> <?php echo  $stats['photo_count'] ?><br/><?php endif; ?>
            <?php if($stats['first_article']): ?><strong>First article:</strong> <?php echo  date("F j, Y",strtotime($stats['first_article'])) ?><br/><?php endif; ?>
            <?php if($stats['latest_article']): ?><strong>Latest article:</strong> <?php echo  date("F j, Y",strtotime($stats['latest_article'])) ?><br/><?php endif; ?>
            <?php if($stats['first_photo']): ?><strong>First image:</strong> <?php echo  date("F j, Y",strtotime($stats['first_photo'])) ?><br/><?php endif; ?>
            <?php if($stats['latest_photo']): ?><strong>Latest image:</strong> <?php echo  date("F j, Y",strtotime($stats['latest_photo'])) ?><br/><?php endif; ?>
            <br/>
            <?php if(!empty($author->bio)): ?><?php echo  $author->bio ?><?php endif; ?>
        </div>
        
        <?php
            if(count($photos) > 1){
                $photo_view_data = array('article' => null, 'photos' => $photos);
                $this->load->view('template/carousel', $photo_view_data);
            }
        ?>
        
    </header>
        
    <section id="articles" class="authorsection">

        <?php
        // calculated widths of divs depending on how many columns we'll have
        $columns = 0;
        $colwidth = 100;
        if(!empty($popular)) $columns++;
        if(!empty($longreads)) $columns++;
        if(!empty($collaborators)) $columns++;
        if(!empty($series)) $columns++;
        if($columns) $colwidth=(1/$columns)*100;
        ?>
        <style>
        /* FOR NON-TABLETS */
            @media all and (min-width: 961px) {
            .statblock {
                width: <?php echo $colwidth?>%;
            }
        }
        </style>

        <?php if(!empty($popular)): ?>
        <div class="statblock">
            <h2>Popular</h2>
            <?php $blocktype = array(
                "blocks"=>$popular,
                "articles"=>TRUE,
                "fullwidth"=>TRUE);?>
            <?php $this->load->view('template/smalltile', $blocktype);?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($longreads)): ?>
        <div class="statblock">
            <h2>Longreads</h2>
            <?php $blocktype = array(
                "blocks"=>$longreads,
                "articles"=>TRUE,
                "fullwidth"=>TRUE);?>
            <?php $this->load->view('template/smalltile', $blocktype);?>
        </div>
        <?php endif; ?>

        <?php if(!empty($collaborators)): ?>
        <div class="statblock">
            <h2>Collaborators</h2>
            <?php $blocktype = array(
                "blocks"=>$collaborators,
                "collab"=>TRUE,
                "articles"=>FALSE,
                "fullwidth"=>TRUE);?>
            <?php $this->load->view('template/smalltile', $blocktype);?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($series)): ?>
        <div class="statblock">
            <h2>Columns</h2>
            <?php $blocktype = array(
                "blocks"=>$series,
                "collab"=>FALSE,
                "serie"=>TRUE,
                "fullwidth"=>TRUE);?>
            <?php $this->load->view('template/smalltile', $blocktype);?>
        </div>
        <?php endif; ?>
        
        <div class="clear"></div>
        
        <?php if(!empty($articles)): ?>
        <h2>All articles</h2>
            <?php $blocktype = array(
                "blocks"=>$articles,
                "twotier"=>TRUE);?>
            <?php $this->load->view('template/articleblock', $blocktype);?>
        <?php endif; ?>
        
    </section>
    
</div>

<?php $this->load->view('template/bodyfooter', $footerdata); ?>

<?php //$this->load->view('bonus/bonusbar', TRUE); ?>

<?php if(count($photos) > 1): ?>
    <!-- SwipeView. Only needed for slideshows. -->
    <script type="text/javascript" src="<?php echo  base_url() ?>js/swipeview-mwidmann.js"></script>
    <script type="text/javascript">
    var    carousel,
        el,
        i,
        page,
        hasInteracted = false,
        dots = document.querySelectorAll('#swipeview_nav li'),
        slides = [
            <?php foreach($photos as $key => $photo): ?>
                <?php if($key > 0): ?>,<?php endif; ?>
                '<div class="swipeview-image" style="background:url(<?php echo  base_url() ?>images/<?php echo  $photo->date ?>/<?php echo  $photo->filename_large ?>)"></div>'
                    +'<figcaption>'
                    + '<p class="photocaption"><?php echo  addslashes(trim(str_replace(array("\r\n", "\n", "\r"),"<br/>",$photo->caption))); ?> <?php echo  anchor("article/".$photo->article_id, addslashes(trim($photo->title))) ?></p>'
                    +'</figcaption>'
            <?php endforeach; ?>
        ];
    
    carousel = new SwipeView('#swipeview_wrapper', {
        numberOfPages: slides.length,
        hastyPageFlip: true
    });
    
    // Load initial data
    for (i=0; i<3; i++) {
        page = i==0 ? slides.length-1 : i-1;
    
        el = document.createElement('span');
        el.innerHTML = slides[page];
        carousel.masterPages[i].appendChild(el)
    }
    
    carousel.onFlip(function () {
        var el,
            upcoming,
            i;
    
        for (i=0; i<3; i++) {
            upcoming = carousel.masterPages[i].dataset.upcomingPageIndex;
    
            if (upcoming != carousel.masterPages[i].dataset.pageIndex) {
                el = carousel.masterPages[i].querySelector('span');
                el.innerHTML = slides[upcoming];
            }
        }
        
        document.querySelector('#swipeview_nav .selected').className = '';
        dots[carousel.pageIndex].className = 'selected';
    });
    
    
    // timer for carousel autoplay
    function loaded() {
        var interval = setInterval(function () { 
                if(!hasInteracted) carousel.next(); 
            }, 5000); 
        
    }
    document.addEventListener('DOMContentLoaded', loaded, false);
    
    </script>
<?php endif; ?>

</body>

</html>
