<?php if(!chromeless()):?>
    <header id="mainhead">
        <div id="head-content">
            <h1 id="wordmark"><a href="http://bowdoinorient.com"><span class="super">The</span> Bowdoin Orient</a></h1>

                    
            <nav id="mainnav" class="hidetablet">
                <ul>
                    <?php if($this->uri->segment(1) == "" || $this->uri->segment(1) == "browse"): ?>
                        <li><a href="#News">News</a></li>
                        <li><a href="#Opinion">Opinion</a></li>
                        <li><a href="#Features">Features</a></li>
                        <li><a href="#Arts & Entertainment">A&E</a></li>
                        <li><a href="#Sports">Sports</a></li>
                        <li><a href="#Featured">★</a></li>
                    <?php endif; ?>
                    <?php if($this->uri->segment(1) == "article" && !empty($section_id)): ?>
                        <li class="<?php echo  ($section_id == "1" ? "active" : "inactive"); ?>"><a href="/section/news">News</a></li>
                        <li class="<?php echo  ($section_id == "2" ? "active" : "inactive"); ?>"><a href="/section/opinion">Opinion</a></li>
                        <li class="<?php echo  ($section_id == "3" ? "active" : "inactive"); ?>"><a href="/section/features">Features</a></li>
                        <li class="<?php echo  ($section_id == "4" ? "active" : "inactive"); ?>"><a href="/section/arts-entertainment">A&E</a></li>
                        <li class="<?php echo  ($section_id == "5" ? "active" : "inactive"); ?>"><a href="/section/sports">Sports</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <?php if  (!isset($viewtype) || $viewtype != "feature"): ?>
        <div id="subnavbar">
            <?php if(isset($date)):?>
                <span id="lastupdated"><?php echo date("F j, Y",strtotime($date))?></span>
                <div id="datepicker"></div> &middot; 
            <?php endif;?>
            <span class="hidemobile">
            <?php if(isset($volume) && isset($issue_number)):?>
                <?php if(!empty($previssue)):?><a href="<?php echo site_url()?>browse/<?php echo $previssue->issue_date?>" class="issue-nav-arrow">&#x25C4;</a> <?php endif;?>
                <?php if(isset($issue) && !empty($issue->scribd)): ?><a href="http://www.scribd.com/doc/<?php echo $issue->scribd?>" class="scribd-link" target="new"><?php endif; ?>Vol. <?php echo $volume?>, No. <?php echo $issue_number?><?php if(isset($issue) && !empty($issue->scribd)): ?></a><?php endif; ?> 
                <?php if(!empty($nextissue)):?><a href="<?php echo site_url()?>browse/<?php echo $nextissue->issue_date?>" class="issue-nav-arrow">&#x25BA;</a> <?php endif;?>&middot;
            <?php endif;?>
            </span>
            <a href="/random">Random <img src="<?php echo base_url()?>img/icon-shuffle.svg" type="image/svg+xml" class="" height="15" width="15" style="margin-bottom: -3px;" title="Dmitry Baranovskiy, from The Noun Project"></a>
            <span class="onlymobile">&middot; <?php echo anchor('search', 'Search'); ?></span>
            <span id="pages" class="hidemobile">
                <a href="http://bowdoinorient.com/about">About</a> &middot; 
                <a href="http://bowdoinorient.com/subscribe">Subscribe</a> &middot; 
                <a href="http://bowdoinorient.com/advertise">Advertise</a> &middot; 
                <a href="http://bowdoinorient.com/contact">Contact</a>
            </span>
        </div>

            <div id="alertbar">
                <div class="alert">This is the Bowdoin Orient's archive site. Content from before 2017 is preserved on this site, but there are no updates.</div>
            </div>
    <?php endif; ?>
<?php endif; ?> 

<!--[if lt IE 9]>
    <div id="alertbar">
        <div class='alert urgent'>Your browser is out-of-date, and parts of the Orient site may not work properly. <a href="http://whatbrowser.org/">Upgrade your browser</a>. If you can't install a new browser, <a href='http://www.google.com/chromeframe/?redirect=true'>try Chrome Frame</a>.</div>
    </div>
<![endif]--> 
