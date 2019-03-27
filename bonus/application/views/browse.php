<?php $this->load->view('template/head'); ?>

<body>

<?php $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    <!-- Below-the-fold sidebar -->
    <div id="sidebar" class="hidetablet">

        <div id="twitter-widget" class="hidetablet">
            <a class="twitter-timeline" href="https://twitter.com/bowdoinorient" data-widget-id="265861494951002113">Tweets by @bowdoinorient</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>

        <!-- ADS -->
        <?php if($ad): ?>
            <h2>Sponsored</h2>
            <?php if(isset($ad->link)):?>
                <a href="<?php echo $ad->link?>">
            <?php endif; ?>
            <?php if ($ad->type == "html"): ?>
                <div class="ad">
                    <?php echo  file_get_contents(base_url()."ads/".$ad->filename); ?>
                </div>
            <?php elseif ($ad->type == "image"): ?>
                <img class="ad" src="<?php echo base_url()."ads/".$ad->filename?>"/>
            <?php endif; ?>
            <?php if(isset($ad->link)):?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <!-- end ads -->

        <!-- Begin MailChimp Signup Form -->
        <div id="mc_embed_signup">
            <form action="http://bowdoinorient.us4.list-manage.com/subscribe/post?u=eab94f63abe221b2ef4a4baec&amp;id=739fef0bb9" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
            <h2 style="margin-top:0;margin-bottom:5px;">Weekly newsletter</h2>
            <div class="mc-field-group">
                <input class="email" type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Email address">
                <input class="button" type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
            </div>
            </form>
        </div>
        <!-- end MailChimp -->

        <!-- Scribd issue download -->
        <?php if($scribd_thumb_url): ?>
        <h2>Download issue</h2>
        <div class="scribd_block">
            <a href="http://www.scribd.com/doc/<?php echo $issue->scribd?>" target="new">
            <img src="<?php echo $scribd_thumb_url?>" class="issue_thumb">
            Volume <?php echo $issue->volume;?><br/>
            Number <?php echo $issue->issue_number;?><br/>
            <?php echo date("F j, Y",strtotime($issue->issue_date))?>
            </a>
        </div>
        <?php endif; ?>
        <!-- end Scribd -->

        <!-- Disqus recent comments -->
        <div id="recentcomments" class="dsq-widget">
            <h2 class="dsq-widget-title">Recent Comments</h2>
            <script type="text/javascript" src="http://disqus.com/forums/bowdoinorient/recent_comments_widget.js?num_items=8&hide_avatars=1&avatar_size=24&excerpt_length=140"></script>
        </div>
        <!-- End Disqus -->

    </div>

    <section id="bignews">
        <div id="lead" class="hidemobile">
            <div class="dates"><?php echo dateify($homepage->leadstory->date, $date)?></div>
            <?php if($homepage->leadstory->series): ?><span class="series"><a href="<?php echo base_url().'series/'.$homepage->leadstory->series?>"><?php echo $homepage->leadstory->seriesname?>:</span></a><?php endif; ?>
            <h3><a href="<?php echo site_url()?>article/<?php echo $homepage->leadstory->id?>"><?php echo $homepage->leadstory->title?></a></h3>
            <span class="bignews-subtitle"><?php echo $homepage->leadstory->subtitle?></span>
            <p><?php echo $homepage->leadstory->excerpt?></p>
            <div class="bonus-overlay <?php if(!bonus()):?>dnone<?php endif;?>">
                <button class="bonus-change <?php if(!bonus()):?>dnone<?php endif;?>" data-container="1">Change</button>
            </div>
        </div>
        <div class="lead overlay"></div>
        <div id="photo">
            <div id="bigphoto">
                <?php if (count($homepage->carousel->photos)==1): ?>
                    <div class="single-photo" style="background-image:url('<?php echo base_url()?>images/<?php echo $homepage->carousel->date?>/<?php echo $homepage->carousel->photos[0]->filename_large?>')"></div>
                <?php else: ?>
                    <div id='slider' class='swipe'>
                        <div class='swipe-wrap'>
                            <?php foreach ($homepage->carousel->photos as $photo): ?>
                                <div class="carousel-photo" style="background-image:url('<?php echo base_url()?>images/<?php echo $homepage->carousel->date?>/<?php echo $photo->filename_large?>')"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="caption">
                <div class="dates"><?php echo dateify($homepage->carousel->date, $date)?></div>
                <h3><a href="<?php echo site_url()?>article/<?php echo $homepage->carousel->id?>"><?php echo $homepage->carousel->title?></a></h3>
            </div>
            <span class="bignews-subtitle"><?php echo $homepage->carousel->subtitle?></span>
            <div class="bonus-overlay <?php if(!bonus()):?>dnone<?php endif;?>">
                <button class="bonus-change <?php if(!bonus()):?>dnone<?php endif;?>" data-container="2">Change</button>
            </div>
        </div>
        <div class="photo overlay"></div>
        <div id="teasers" class="hidetablet">
            <?php $i = 3; ?>
            <?php foreach($homepage->teasers as $teaser): ?>
                <div class="teaser">
                    <div class="dates"><?php echo dateify($teaser->date, $date)?></div>
                    <?php if($teaser->series): ?><span class="series"><a href="<?php echo base_url().'series/'.$teaser->series?>"><?php echo $teaser->seriesname?>:</span></a><?php endif; ?>
                    <h4 class="teaser-hed"><a href="<?php echo site_url()?>article/<?php echo $teaser->id?>"><?php echo $teaser->title?></a></h4>
                    <span class="bignews-subtitle"><?php echo $teaser->subtitle?></span>
                    <div class="bonus-overlay <?php if(!bonus()):?>dnone<?php endif;?>">
                        <button class="bonus-change <?php if(!bonus()):?>dnone<?php endif;?>" data-container="<?php echo $i?>">Change</button>
                    </div>
                </div>
                <?php $i++; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECTIONS -->
    <?php foreach($sections as $section): ?>
        <?php if(!empty($articles[$section->name])): ?>
        <section id="<?php echo $section->name?>" class="issuesection">
            <h2><?php echo $section->name?><?php if(bonus()): ?><a href="<?php echo site_url()?>article/add/<?php echo $issue->volume?>/<?php echo $issue->issue_number?>/<?php echo $section->id?>"><button class="bonusbutton" id="addarticlebutton">Add article</button></a><?php endif; ?></h2>
            <?php $blocktype = array(
                "blocks"=>$articles[$section->name],
                "twotier"=>TRUE,
                "dateified"=>TRUE,
                "dateoverlay"=>TRUE);?>
            <?php $this->load->view('template/articleblock', $blocktype);?>
        </section>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php $this->load->view('template/bodyfooter', $footerdata); ?>

<?php $this->load->view('bonus/bonusbar', TRUE); ?>

<script type="text/javascript">
    $(document).ready(function(){
        window.mySwipe = Swipe($('#slider')[0], {
            speed: 300,
            auto: 5000,
        });
    });
</script>

<script type="text/javascript">
    var selected = 0;
    <?php if(bonus()):?>
        input_articles = '<?php $v=$this->load->view("template/atf-chooser", $articlelists, true); echo(str_replace("'", "\\'", str_replace("\"", "\\\"", str_replace(array("\n", "\r"), "", $v)))); ?>';
        input_photos = '<?php $v=$this->load->view("template/atf-chooser", $photolists, true); echo(str_replace("'", "\\'", str_replace("\"", "\\\"", str_replace(array("\n", "\r"), "", $v)))); ?>';
    <?php endif;?>

    $("button.bonus-change").click(function(){
        $button = $(this);
        if($button.data("container")==2){
            inputlist = input_photos;
            photosonly = "story with a photo";
        } else {
            inputlist = input_articles;
            photosonly = "story";
        }
        vex.dialog.open({
            message: 'Pick a '+photosonly+' for this container:',
            input: inputlist,          
            callback: function(data) {
                if (data !== false && selected > 0) {
                    $.ajax({
                        url:    "/browse/ajax_set_atf/"+$button.data("container"),
                        type:   "POST",
                        data:   "id="+selected,
                        dataType: 'json',
                        success: function(data, textStatus, jqXHR) {
                            location.reload(true);
                        },
                        error: function(err){
                            console.log(err);
                            vex.dialog.alert("Error: " + err.responseText);
                        }
                    });
                }
            }
        });

        $(".choose-list").click(function(){
            $(".article-list").each(function(){
                $(this).hide();
            });
            $(".article-list#"+$(this).attr("id")).show();
        });

        $("p.article-choice").click(function(){
            pick($(this));
        });

        $("div.img-preview").click(function(){ 
            pick($(this).next());
        });

        function pick($element){
            $("p.article-choice").each(function(){
                $(this).css("color", "#444444");
            });
            $element.css("color", "blue");
            selected = $element.attr("id"); 
        }

    });
</script>

</body>

</html>
