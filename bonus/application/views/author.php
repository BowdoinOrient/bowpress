<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
            
    <header class="authorheader">
                
        <? if(!empty($author->photo)): ?>
            <figure class="authorpic"><img src="<?=base_url().'images/authors/'.$author->photo?>"></figure>
        <? endif; ?>
                
        <div class="authorstats">
            <h2 class="authorname"><?=$author->name?></h2>
            
            <? if($stats['article_count']): ?><strong>Number of articles:</strong> <?= $stats['article_count'] ?><br/><? endif; ?>
            <? if($stats['photo_count']): ?><strong>Number of photos:</strong> <?= $stats['photo_count'] ?><br/><? endif; ?>
            <? if($stats['first_article']): ?><strong>First article:</strong> <?= date("F j, Y",strtotime($stats['first_article'])) ?><br/><? endif; ?>
            <? if($stats['latest_article']): ?><strong>Latest article:</strong> <?= date("F j, Y",strtotime($stats['latest_article'])) ?><br/><? endif; ?>
            <? if($stats['first_photo']): ?><strong>First image:</strong> <?= date("F j, Y",strtotime($stats['first_photo'])) ?><br/><? endif; ?>
            <? if($stats['latest_photo']): ?><strong>Latest image:</strong> <?= date("F j, Y",strtotime($stats['latest_photo'])) ?><br/><? endif; ?>
            <br/>
            <? if(!empty($author->bio)): ?><?= $author->bio ?><? endif; ?>
        </div>
        
        <?
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
                width: <?=$colwidth?>%;
            }
        }
        </style>

        <? if(!empty($popular)): ?>
        <div class="statblock">
            <h2>Popular</h2>
            <?$blocktype = array(
                "blocks"=>$popular,
                "articles"=>TRUE,
                "fullwidth"=>TRUE);?>
            <?$this->load->view('template/smalltile', $blocktype);?>
        </div>
        <? endif; ?>
        
        <? if(!empty($longreads)): ?>
        <div class="statblock">
            <h2>Longreads</h2>
            <?$blocktype = array(
                "blocks"=>$longreads,
                "articles"=>TRUE,
                "fullwidth"=>TRUE);?>
            <?$this->load->view('template/smalltile', $blocktype);?>
        </div>
        <? endif; ?>

        <? if(!empty($collaborators)): ?>
        <div class="statblock">
            <h2>Collaborators</h2>
            <?$blocktype = array(
                "blocks"=>$collaborators,
                "collab"=>TRUE,
                "articles"=>FALSE,
                "fullwidth"=>TRUE);?>
            <?$this->load->view('template/smalltile', $blocktype);?>
        </div>
        <? endif; ?>
        
        <? if(!empty($series)): ?>
        <div class="statblock">
            <h2>Columns</h2>
            <?$blocktype = array(
                "blocks"=>$series,
                "collab"=>FALSE,
                "serie"=>TRUE,
                "fullwidth"=>TRUE);?>
            <?$this->load->view('template/smalltile', $blocktype);?>
        </div>
        <? endif; ?>
        
        <div class="clear"></div>
        
        <? if(!empty($articles)): ?>
        <h2>All articles</h2>
            <?$blocktype = array(
                "blocks"=>$articles,
                "twotier"=>TRUE);?>
            <?$this->load->view('template/articleblock', $blocktype);?>
        <? endif; ?>
        
    </section>
    
</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? //$this->load->view('bonus/bonusbar', TRUE); ?>

<? if(count($photos) > 1): ?>
    <!-- SwipeView. Only needed for slideshows. -->
    <script type="text/javascript" src="<?= base_url() ?>js/swipeview-mwidmann.js"></script>
    <script type="text/javascript">
    var    carousel,
        el,
        i,
        page,
        hasInteracted = false,
        dots = document.querySelectorAll('#swipeview_nav li'),
        slides = [
            <? foreach($photos as $key => $photo): ?>
                <? if($key > 0): ?>,<? endif; ?>
                '<div class="swipeview-image" style="background:url(<?= base_url() ?>images/<?= $photo->date ?>/<?= $photo->filename_large ?>)"></div>'
                    +'<figcaption>'
                    + '<p class="photocaption"><?= addslashes(trim(str_replace(array("\r\n", "\n", "\r"),"<br/>",$photo->caption))); ?> <?= anchor("article/".$photo->article_id, addslashes(trim($photo->title))) ?></p>'
                    +'</figcaption>'
            <? endforeach; ?>
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
<? endif; ?>

</body>

</html>
