<?if(!chromeless()):?>
    <header id="mainhead">
        <div id="head-content">
            <h1 id="wordmark"><a href="http://bowdoinorient.com"><span class="super">The</span> Bowdoin Orient</a></h1>

                    
            <nav id="mainnav" class="hidetablet">
                <ul>
                    <? if($this->uri->segment(1) == "" || $this->uri->segment(1) == "browse"): ?>
                        <li><a href="#News">News</a></li>
                        <li><a href="#Opinion">Opinion</a></li>
                        <li><a href="#Features">Features</a></li>
                        <li><a href="#Arts & Entertainment">A&E</a></li>
                        <li><a href="#Sports">Sports</a></li>
                        <li><a href="#Featured">★</a></li>
                    <? endif; ?>
                    <? if($this->uri->segment(1) == "article" && !empty($section_id)): ?>
                        <li class="<?= ($section_id == "1" ? "active" : "inactive"); ?>"><a href="/section/news">News</a></li>
                        <li class="<?= ($section_id == "2" ? "active" : "inactive"); ?>"><a href="/section/opinion">Opinion</a></li>
                        <li class="<?= ($section_id == "3" ? "active" : "inactive"); ?>"><a href="/section/features">Features</a></li>
                        <li class="<?= ($section_id == "4" ? "active" : "inactive"); ?>"><a href="/section/arts-entertainment">A&E</a></li>
                        <li class="<?= ($section_id == "5" ? "active" : "inactive"); ?>"><a href="/section/sports">Sports</a></li>
                    <? endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <? if  (!isset($viewtype) || $viewtype != "feature"): ?>
        <div id="subnavbar">
            <?if(isset($date)):?>
                <span id="lastupdated"><?=date("F j, Y",strtotime($date))?></span>
                <div id="datepicker"></div> &middot; 
            <?endif;?>
            <span class="hidemobile">
            <?if(isset($volume) && isset($issue_number)):?>
                <? if(!empty($previssue)):?><a href="<?=site_url()?>browse/<?=$previssue->issue_date?>" class="issue-nav-arrow">&#x25C4;</a> <?endif;?>
                <? if(isset($issue) && !empty($issue->scribd)): ?><a href="http://www.scribd.com/doc/<?=$issue->scribd?>" class="scribd-link" target="new"><? endif; ?>Vol. <?=$volume?>, No. <?=$issue_number?><? if(isset($issue) && !empty($issue->scribd)): ?></a><? endif; ?> 
                <? if(!empty($nextissue)):?><a href="<?=site_url()?>browse/<?=$nextissue->issue_date?>" class="issue-nav-arrow">&#x25BA;</a> <?endif;?>&middot;
            <?endif;?>
            </span>
            <a href="/random">Random <img src="<?=base_url()?>img/icon-shuffle.svg" type="image/svg+xml" class="" height="15" width="15" style="margin-bottom: -3px;" title="Dmitry Baranovskiy, from The Noun Project"></a>
            <span class="onlymobile">&middot; <?=anchor('search', 'Search'); ?></span>
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
    <? endif; ?>
<? endif; ?> 

<!--[if lt IE 9]>
    <div id="alertbar">
        <div class='alert urgent'>Your browser is out-of-date, and parts of the Orient site may not work properly. <a href="http://whatbrowser.org/">Upgrade your browser</a>. If you can't install a new browser, <a href='http://www.google.com/chromeframe/?redirect=true'>try Chrome Frame</a>.</div>
    </div>
<![endif]--> 
