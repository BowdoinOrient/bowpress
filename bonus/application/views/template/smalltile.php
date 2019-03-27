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
    <?php foreach($blocks as $block):?>
    <li class="smalltile <?php if(!empty($fullwidth)):?>fullwidth <?php endif;?><?php if(!empty($autoheight)):?>autoheight <?php endif;?>">
        <?php if(!empty($serie)):?>
            <a href="<?php echo base_url()?>series/<?php echo $block->series?>">
                <h3><?php echo $block->name?></h3>
            </a>
        <?php elseif(!empty($contrib)):?>
            <a href="<?php echo base_url()?>author/<?php echo $block->author_id?>" title="<?php echo $block->contrib_count?> contribution<?php echo  ($block->contrib_count > 1 ? 's' : '') ?>">
                <h3><?php echo $block->name?></h3>
            </a>
        <?php elseif(!empty($collab)):?>
            <a href="<?php echo base_url()?>author/<?php echo $block->author_id?>" title="<?php echo $block->collab_count?> collaboration<?php echo  ($block->collab_count > 1 ? 's, including' : ':') ?> '<?php echo $block->title?>' ">
                <h3><?php echo $block->name?></h3>
            </a>
        <?php elseif(!empty($articles)):?>
            <a href="<?php echo base_url()?>article/<?php echo $block->id?>">
                <?php if(isset($dateified)): //only articles are ever dateified?>
                    <div class="dateified"><?php echo dateify($block->date, $date)?></div>
                <?php endif;?>
                <h3>
                    <?php if(isset($block->series)):?>
                        <span class="series"><?php echo $block->series?></span>
                    <?php endif; ?>
                    <?php echo $block->title?>
                </h3>
                <?php if(isset($block->subtitle) && !empty($subtitle)): ?>
                    <h4><?php echo  $block->subtitle ?></h4><?php endif; ?>
                <?php if(isset($block->excerpt) && !empty($excerpt)):?>
                    <div class="excerpt"><?php echo $block->excerpt?></div>
                <?php endif;?>
            </a>
        <?php endif;?>
    </li>
    <?php endforeach; ?>
</ul>
