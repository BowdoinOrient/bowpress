<?php
$headdata = new stdClass();
$headdata->viewtype = "article";
$this->load->view('template/head', $headdata); ?>

<body>

    <? $this->load->view('template/bodyheader', $headerdata); ?>

    <div id="content">

        <article id="mainstory" data-article-id="<?=$article->id?>">

            <header>
                <hgroup class="articletitle-group">

                <!-- NEXT / PREV -->
                <div class="article_header_nav hidetablet hidemobile">
                    <? if(!empty($series_previous)): ?>
                        <?$leftblock = array(
                            "blocks"=>$series_previous,
                            "leftmargin"=>TRUE,
                            "rightmargin"=>FALSE);?>
                        <?$this->load->view('template/articleblock', $leftblock);?>
                        <script type="text/javascript">if(!isFullyVisible($('.leftmargin')))$(".leftmargin").hide();</script>
                    <? endif;?>
                    <? if(!empty($series_next)): ?>
                        <?$rightblock = array(
                            "blocks"=>$series_next,
                            "rightmargin"=>TRUE,
                            "leftmargin"=>FALSE);?>
                        <?$this->load->view('template/articleblock', $rightblock);?>
                        <script type="text/javascript">if(!isFullyVisible($('.rightmargin')))$(".rightmargin").hide();</script>
                    <? endif; ?>
                </div>

                <? if($article->series || bonus()): ?>
                    <h3 id="series" class="series"<?if(bonus()):?> contenteditable="true" title="Series"<?endif;?>>
                <? if(!bonus()): ?><a href="<?=site_url()?>series/<?=$series->id?>"><? endif; ?>
                <?=$series->name?>
                <? if(!bonus()): ?></a><? endif; ?>
                    </h3>
                <? endif; ?>

                <h2 id="articletitle" class="articletitle <?= ($article->published ? '' : 'draft'); ?>"<?if(bonus()):?> contenteditable="true" title="Title"<?endif;?>><?=$article->title?></h2>
                <? if(bonus()): ?><div id="title" class="charsremaining"></div><? endif; ?>
                <h3 id="articlesubtitle" class="articlesubtitle"<?if(bonus()):?> contenteditable="true" title="Subtitle"<?endif;?>><? if(isset($article->subtitle)): ?><?=$article->subtitle?><? endif; ?></h3>
                <? if(bonus()): ?><div id="subtitle" class="charsremaining"></div><? endif; ?>

            </hgroup>

        <div id="authorblock">
            <? if(bonus() && $series->name != "Editorial"): ?>
                <div class="opinion-notice"><input type="checkbox" name="opinion" value="opinion" <? if($article->opinion): ?>checked="checked"<? endif; ?> /> Does this piece represent the opinion of the author?</div>
            <? endif; ?>
            <? if($series->name == "Editorial"): ?>
                <object data="<?=base_url()?>img/icon-opinion.svg" type="image/svg+xml" class="opinion-icon" height="20" width="20" title="Plinio Fernandes, from The Noun Project"></object>
                <div class="opinion-notice">This piece represents the opinion of <span style="font-style:normal;">The Bowdoin Orient</span> editorial board.</div>
            <? endif; ?>
            <? if($authors): ?>
                <? if($article->opinion == '1' && !bonus()): ?>
                    <object data="<?=base_url()?>img/icon-opinion.svg" type="image/svg+xml" class="opinion-icon" height="20" width="20" title="Plinio Fernandes, from The Noun Project"></object>
                    <div class="opinion-notice">This piece represents the opinion of the author<?if(count($authors)>1):?>s<?endif;?>:</div>
                <? endif; ?>
                <? foreach($authors as $key => $author): ?>
                    <a href="<?=site_url()?>author/<?=$author->authorid?>">
                        <div id="author<?=$author->articleauthorid?>" class="authortile<? if(bonus()):?> bonus<? endif; ?> <?if($article->opinion == '1'):?>opinion<? endif; ?>">
                            <? if(bonus()): ?><div id="deleteAuthor<?=$author->articleauthorid?>" class="delete">&times;</div><? endif; ?>
                            <? if(!empty($author->photo) && $article->opinion): ?><img src="<?=base_url().'images/authors/'.$author->photo?>" class="authorpic"><? endif; ?>
                            <div class="authortext">
                                <div class="articleauthor"><?=$author->authorname?></div>
                                <div class="articleauthorjob"><?=$author->jobname?></div>
                            </div>
                        </div>
                    </a>
                <? endforeach; ?>
            <? endif; ?>
            <? if(bonus()): ?>
                <div class="authortile bonus <?if($article->opinion == '1'):?>opinion<? endif; ?>">
                    <div class="articleauthor" id="addauthor" contenteditable="true" title="Author"></div>
                    <div class="articleauthorjob" id="addauthorjob" contenteditable="true" title="Author job"></div>
                </div>
            <? endif; ?>
        </div>

        <p class="articledate"><time pubdate datetime="<?=$article->date?>"><?=date("F j, Y",strtotime($article->date))?></time></p>
            </header>

            <!-- catcher is used to trigger sticky sidebar, currently disabled (see below) -->
            <div id="article-sidebar-catcher"></div>
            <!-- sidebar contains photos, videos, and other attachments -->
            <div id="article-sidebar">
                <div id="article-attachments">
                    <?
                        if($photos) {
                            if(count($photos) == 1 || bonus()) {
                                foreach($photos as $key => $photo) {
                                    $photo_view_data = array('article' => $article, 'photo' => $photo);
                                    $this->load->view('template/attachment-photo', $photo_view_data);
                                }
                            } else {
                                if(count($photos) > 1){
                                    $photo_view_data = array('article' => $article, 'photos' => $photos);
                                    $this->load->view('template/carousel', $photo_view_data);
                                }
                            }
                        }
                        if($attachments) { //looks through the attachments and sees what's there
                            $hasYoutube = false;
                            $youtubePlaylist = array();

                            $hasVimeo = false;
                            $vimeos = array();

                            $hasHTML = false;
                            $HTMLs = array();

                            // looks through each attachment
                            foreach($attachments as $key => $attachment) {
                                // spots youtube. holds onto the first, sticks the rest in a playlist
                                if($attachment->type == 'youtube') {
                                    if(!$hasYoutube) {
                                        // hold onto the attachment
                                        $youtube = $attachment;
                                        $hasYoutube = true;
                                    } else {
                                        // if it's not first youtube video, push to playlist
                                        $youtubePlaylist[] = $attachment->content1;
                                    }
                                }

                                // spots and handles vimeos
                                if($attachment->type == 'vimeo') {
                                    $hasVimeo = true;
                                    $vimeos[] = $attachment;
                                }

                                // spots and handles raw html
                                // note that there's currently no way to create an html attachment in bonus
                                // but you can do it straight through the database if you want
                                if($attachment->type == 'html') {
                                    $hasHTML = true;
                                    $HTMLs[] = $attachment;
                                }
                            }

                            // if there's at least one youtube video, load the first and put the rest in the playlist
                            if($hasYoutube) {
                                // serializes the playlist (so you have comma-separated IDs: 124234,43t346,3i4ngiu...)
                                $youtube->playlist = join($youtubePlaylist,',');
                                // load the actual embedded player
                                $this->load->view('template/attachment-video', $youtube);
                            }
                            if($hasVimeo) { foreach($vimeos as $vimeo) { $this->load->view('template/attachment-video', $vimeo); } }
                            if($hasHTML) { foreach($HTMLs as $html) { $this->load->view('template/attachment-html', $html); } }
                        }
                    ?>
                </div>
                <div id="bonus-attachments">
                    <? if(bonus()): ?>
                        <!-- image upload -->
                        <figure class="articlemedia mini">
                            <div id="dnd-holder" class="bonus-attachment">
                                <!-- imageupload input has opacity set to zero, width and height set to 100%, so you can click anywhere to upload -->
                                <input id="imageupload" class="imageupload" type=file accept="image/gif,image/jpeg,image/png">
                                <div id="dnd-instructions">
                                    <img src="<?=base_url()?>img/icon-uploadphoto.png" type="image/svg+xml" height="50" width="50" title=""></object>
                                    <br/>Click or drag
                                    <br/>JPG, PNG or GIF
                                </div>
                            </div>
                            <figcaption class="bonus">
                                <p id="photocreditbonus" class="photocredit" contenteditable="true" title="Photographer"></p>
                                <p id="photocaptionbonus" class="photocaption" contenteditable="true" title="Caption"></p>
                                <p class="hide-photo">Use photo as homepage thumbnail only:  <input class="hide-photo" type="checkbox"></p>
                            </figcaption>
                        </figure>

                        <!-- video attachment -->
                        <figure class="articlemedia mini">
                            <div id="video-attach" class="bonus-attachment">
                                <img src="<?=base_url()?>img/icon-video.png" width="45" title="Thomas Le Bas, from The Noun Project"></object>
                                <form>
                                    <br/><input type="text" style="width:160px" name="video-url" placeholder="YouTube or Vimeo URL"></input>
                                    <br/><button type="submit" id="attach-video">Attach</button>
                                </form>
                            </div>
                        </figure>

                        <!-- HTML inliner -->
                        <figure class="articlemedia mini">
                            <div id="html-attach" class="bonus-attachment">
                                <img src="<?=base_url()?>img/icon-code.png" width="45" title=""></object>
                                <form>
                                    <br/><input type="text" style="width:160px" name="html-code" placeholder="Raw HTML code"></input>
                                    <br/><button type="submit" id="insert-code">Insert</button>
                                </form>
                            </div>
                        </figure>

                    <? endif; ?>
                </div>
            </div>

            <div id="articlebodycontainer">

            <!-- placeholder for table of contents, to be injected by js -->
            <div id="toc_container_catcher"></div>
            <div id="toc_container"></div>

            <div id="articlebody" class="articlebody"<?if(bonus()):?> contenteditable="true" title="Article body"<?endif;?>>
                <? if(!empty($body)): ?>
                <?=$body->body;?>
                <? endif; ?>
            </div>

            </div>
	    <hr>
            <p style="font-style: italic; border-top: 1px solid #CCC; padding-top: 10px;">Comments are permanently closed.</p>
        </article>
    </div>

    <? $this->load->view('template/bodyfooter', $footerdata); ?>
</body>


</html>
