<?php $this->load->view('template/head'); ?>

<body>

<?php $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
            
    <header class="authorheader">
                
        <?php if(!empty($series->photo)): ?>
            <figure class="authorpic"><img src="<?php echo base_url().'images/series/'.$series->photo?>"></figure>
        <?php endif; ?>
        
            <!-- MARGIN-BOTTOM HERE IS TEMPORARY UNTIL HEADER GETS FLESHED OUT -->
        <div class="authorstats" style="margin-bottom:0!important;">
            <h2 class="authorname"><?php echo $series->name?></h2>
            
            <?php if(!empty($series->description)): ?><?php echo  $series->description ?><?php endif; ?>
        </div>
                
    </header>
    
    <section id="articles" class="seriessection">
        
        <?php
        // calculated widths of divs depending on how many columns we'll have
        $columns = 0;
        $colwidth = 100;
        if(!empty($contributors)) $columns++;
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
        
        <?php if(!empty($contributors)): ?>
        <div class="statblock">
            <h2>Contributors</h2>
            <?php $blocktype = array(
                "blocks"=>$contributors,
                "articles"=>FALSE,
                "autoheight"=>TRUE,
                "contrib"=>TRUE);?>
            <?php $this->load->view('template/smalltile', $blocktype);?>
        </div>
        <?php endif; ?>
        
        <h2>All articles</h2>
        <?php $blockparams = array(
            "blocks"=>$articles,
            "twotier"=>TRUE);
        $this->load->view('template/articleblock', $blockparams);?>    
    </section>
    
</div>

<?php $this->load->view('template/bodyfooter', $footerdata); ?>

<?php $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>