<!-- PARAMS:
twotier: is it big
blocks: list of articles to block out
rightmargin: for next series articles
leftmargin: for previous series articles
dateified: does it show a date
medtile: is it medium-sized (body footer only, right now)
dateoverlay: the "1 day ago" red overlay text -->

<ul class="articleblock<?if(!empty($twotier)):?> twotier<?endif;?><?if(!empty($leftmargin)):?> leftmargin<?endif;?><?if(!empty($rightmargin)):?> rightmargin<?endif;?>">
    <?foreach($blocks as $block):?>
    <li class="<? if(!empty($block->filename_small)): ?> backgrounded<? endif; ?><? if(!$block->published): ?> draft<? endif; ?><? if(strtotime($date)-strtotime($block->date) > (7*24*60*60)): ?> old<? endif; ?><?if(!empty($medtile)):?> medtile<?endif;?>"<? if(!empty($block->filename_small) && !isMobile()): ?> style="background:url('<?=base_url().'images/'.$block->date.'/'.$block->filename_small?>')"<? endif; ?>>
        <a href="<?=site_url()?>article/<?=$block->id?>">
            <?if(!empty($dateified)):?>
            <div class="dateified"><?=dateify($block->date, $date)?></div>
            <?endif;?>
            <h3 class="footertitle"><? if($block->series): ?><span class="series"><?=$block->series?>:</span> <? endif; ?><?=$block->title?></h3>
            <? if($block->subtitle): ?>
            <h4><?= $block->subtitle ?></h4><? endif; ?>
            <div class="excerpt"><?=$block->excerpt?></div>
        </a>
    </li>
    <?endforeach;?>
</ul>


