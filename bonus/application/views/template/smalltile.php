<!-- PARAMS:
blocks: list of things to display
fullwidth: display wider than normal
autoheight: add the "autoheight" css class
articles: are we listing articles?
serie: OR series names?
contrib: OR people that contributed to a series?
collab: OR people that worked with a given author?
dateified: show the date overlay?
subtitle: show the subtitle?
excerpt: show the excerpt? -->

<ul class="articleblock">
    <?foreach($blocks as $block):?>
    <li class="smalltile <?if(!empty($fullwidth)):?>fullwidth <?endif;?><?if(!empty($autoheight)):?>autoheight <?endif;?>">
        <?if(!empty($serie)):?>
            <a href="<?=base_url()?>series/<?=$block->series?>">
                <h3><?=$block->name?></h3>
            </a>
        <?elseif(!empty($contrib)):?>
            <a href="<?=base_url()?>author/<?=$block->author_id?>" title="<?=$block->contrib_count?> contribution<?= ($block->contrib_count > 1 ? 's' : '') ?>">
                <h3><?=$block->name?></h3>
            </a>
        <?elseif(!empty($collab)):?>
            <a href="<?=base_url()?>author/<?=$block->author_id?>" title="<?=$block->collab_count?> collaboration<?= ($block->collab_count > 1 ? 's, including' : ':') ?> '<?=$block->title?>' ">
                <h3><?=$block->name?></h3>
            </a>
        <?elseif(!empty($articles)):?>
            <a href="<?=base_url()?>article/<?=$block->id?>">
                <?if(isset($dateified)): //only articles are ever dateified?>
                    <div class="dateified"><?=dateify($block->date, $date)?></div>
                <?endif;?>
                <h3>
                    <?if(isset($block->series)):?>
                        <span class="series"><?=$block->series?></span>
                    <?endif; ?>
                    <?=$block->title?>
                </h3>
                <?if(isset($block->subtitle) && !empty($subtitle)): ?>
                    <h4><?= $block->subtitle ?></h4><? endif; ?>
                <?if(isset($block->excerpt) && !empty($excerpt)):?>
                    <div class="excerpt"><?=$block->excerpt?></div>
                <?endif;?>
            </a>
        <?endif;?>
    </li>
    <? endforeach; ?>
</ul>