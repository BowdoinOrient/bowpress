<?php $this->load->view('template/head'); ?>

<body>

<?php $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    
    <article id="mainstory">
        
        <header>
            <hgroup>
                <h2 id="articletitle" class="articletitle">Advanced Search <span style="font-variant:small-caps;color:gray;">beta</span></h2>
                <!--<h3 id="articlesubtitle" class="articlesubtitle"></h3>-->
            </hgroup>            
        </header>
        
        <div id="articlebody" class="articlebody">
        
            <form action="<?php echo site_url()?>advsearch" id="adv-search" method="get">
            
                <input class="" type="text" placeholder="Title" name="title" autofocus <?php if(!empty($searchdata['title'])):?>value="<?php echo  $searchdata['title'] ?>"<?php endif;?>>
                
                <br/><input class="" type="text" placeholder="Author" name="author" <?php if(!empty($searchdata['author'])):?>value="<?php echo  $searchdata['author'] ?>"<?php endif;?>>
                <input class="" type="text" placeholder="Series" name="series" <?php if(!empty($searchdata['series'])):?>value="<?php echo  $searchdata['series'] ?>"<?php endif;?>>
                
                <br/><input class="" type="date" placeholder="Since date" name="since" <?php if(!empty($searchdata['since'])):?>value="<?php echo  $searchdata['since'] ?>"<?php endif;?>>
                – <input class="" type="date" placeholder="Until date" name="until" <?php if(!empty($searchdata['until'])):?>value="<?php echo  $searchdata['until'] ?>"<?php endif;?>>
                
                <br/>Featured: <?php echo  form_checkbox('featured', 'featured', (!empty($searchdata['featured']))); ?>
                
                <br/><button id="submit" type="submit">Search</button>
                
            </form>            
 
        </div>
      
          <?php if(!empty($articles)): ?>
        <section id="results" class="">
            <h2>Results</h2>    
            <?php $blocktype = array(
                "blocks"=>$articles,
                "twotier"=>TRUE,
                "dateified"=>TRUE);?>
            <?php $this->load->view('template/articleblock', $blocktype);?>
        </section>
        <?php elseif(!empty($searchdata)): ?>
        <p>No results.</p>
        <?php endif; ?>
      
      
    </article>

</div>

<?php $this->load->view('template/bodyfooter', $footerdata); ?>

<?php $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>