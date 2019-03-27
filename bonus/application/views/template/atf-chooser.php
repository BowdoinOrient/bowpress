<div class="chooser">
    <div id="latest" class="choose-list left"><p>Latest</p></div>
    <div id="popular_week" class="choose-list center"><p>Pop / Week</p></div>
    <div id="popular_semester" class="choose-list right"><p>Pop / Semester</p></div>
</div>



<?php if(!isset($popular_semester_photo)): ?>

    <ul class="article-list" id="latest">
        <?foreach ($latest as $article):?>
            <li><p class="article-choice" id="<?php echo $article->id?>"><?php echo $article->title?></p></li>
        <?php endforeach;?>
    </ul>

    <ul class="article-list" id="popular_week">
        <?foreach ($popular_week as $article):?>
            <li><p class="article-choice" id="<?php echo $article->id?>"><?php echo $article->title?></p></li>
        <?php endforeach;?>
    </ul>

    <ul class="article-list" id="popular_semester">
        <?foreach ($popular_semester as $article):?>
            <li><p class="article-choice" id="<?php echo $article->id?>"><?php echo $article->title?></p></li>
        <?php endforeach;?>
    </ul>

<?php else: ?>
    <ul class="article-list" id="latest">
        <?foreach ($latest_photo as $article):?>
            <li class="withphotos">
                <div class="img-preview" style="background-image:url('<?php echo base_url()?>images/<?php echo $article->date?>/<?php echo $article->filename_small?>')"></div>
                <p class="article-choice" id="<?php echo $article->id?>"><?php echo $article->title?></p>
            </li>
        <?php endforeach;?>
    </ul>

    <ul class="article-list" id="popular_week">
        <?foreach ($popular_week_photo as $article):?>
            <li class="withphotos">
                <div class="img-preview" style="background-image:url('<?php echo base_url()?>images/<?php echo $article->date?>/<?php echo $article->filename_small?>')"></div>
                <p class="article-choice" id="<?php echo $article->id?>"><?php echo $article->title?></p>
            </li>
        <?php endforeach;?>
    </ul>

    <ul class="article-list" id="popular_semester">
        <?foreach ($popular_semester_photo as $article):?>
            <li class="withphotos">
                <div class="img-preview" style="background-image:url('<?php echo base_url()?>images/<?php echo $article->date?>/<?php echo $article->filename_small?>')"></div>
                <p class="article-choice" id="<?php echo $article->id?>"><?php echo $article->title?></p>
            </li>
        <?php endforeach;?>
    </ul>

<?php endif; ?>