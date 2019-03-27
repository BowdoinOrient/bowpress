<!-- PARAMS:
twotier: is it big
blocks: list of articles to block out
rightmargin: for next series articles
leftmargin: for previous series articles
dateified: does it show a date
medtile: is it medium-sized (body footer only, right now)
dateoverlay: the "1 day ago" red overlay text -->

<ul class="articleblock<?php if(!empty($twotier)):?> twotier<?php endif;?><?php if(!empty($leftmargin)):?> leftmargin<?php endif;?><?php if(!empty($rightmargin)):?> rightmargin<?php endif;?>">
    <?php foreach($blocks as $block):?>
    <li class="<?php if(!empty($block->filename_small)): ?> backgrounded<?php endif; ?><?php if(!$block->published): ?> draft<?php endif; ?><?php if(strtotime($date)-strtotime($block->date) > (7*24*60*60)): ?> old<?php endif; ?><?php if(!empty($medtile)):?> medtile<?php endif;?>"<?php if(!empty($block->filename_small) && !isMobile()): ?> style="background:url('<?php echo base_url().'images/'.$block->date.'/'.$block->filename_small?>')"<?php endif; ?>>
        <a href="<?php echo site_url()?>article/<?php echo $block->id?>">
            <?php if(!empty($dateified)):?>
            <div class="dateified"><?php echo dateify($block->date, $date)?></div>
            <?php endif;?>
            <h3 class="footertitle"><?php if($block->series): ?><span class="series"><?php echo $block->series?>:</span> <?php endif; ?><?php echo $block->title?></h3>
            <?php if($block->subtitle): ?>
            <h4><?php echo  $block->subtitle ?></h4><?php endif; ?>
            <div class="excerpt"><?php echo $block->excerpt?></div>
        </a>
    </li>
    <?php endforeach;?>
</ul>


