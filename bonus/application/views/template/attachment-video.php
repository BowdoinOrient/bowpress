<figure id="attachment<?=$id?>" data-attachment-id="<?=$id?>" data-attachment-type="<?=$type?>" data-playlist="<?=(!empty($playlist) ? $playlist : '')?>" class="articlemedia video-wrapper <?=$type?> <?=(!empty($playlist) ? 'playlist' : '')?> <?= ($big ? 'bigphoto' : '') ?>">

    <? if(bonus()): ?>
        <div id="deleteAttachment<?=$id?>" data-attachment-id="<?=$id?>" class="delete deleteAttachment">&times;</div>
        <div id="bigEnable<?=$id?>"  data-attachment-id="<?=$id?>" data-toggle="true"  class="bigAttachmentToggle <?= ($big ? 'hide' : '') ?>">&#8689;</div>
        <div id="bigDisable<?=$id?>" data-attachment-id="<?=$id?>" data-toggle="false" class="bigAttachmentToggle <?= ($big ? '' : 'hide') ?>">&#8690;</div>
    <? endif; ?>
    
    <?
    $width = ($big ? '890' : '500');
    $height = floor(0.562*$width);
    ?>
    
    <div class="video-container">
    <? if($type == 'youtube'): ?>
        <iframe width="<?=$width?>" height="<?=$height?>" src="http://www.youtube.com/embed/<?=$content1?>?modestbranding=1&rel=0&showinfo=0&theme=light&playlist=<?=(!empty($playlist) ? $playlist.',' : '')?>" frameborder="0" allowfullscreen></iframe>
    <? elseif($type == 'vimeo'): ?>
        <iframe src="http://player.vimeo.com/video/<?=$content1?>?portrait=0" width="<?=$width?>" height="<?=$height?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    <? endif; ?>
    </div>
    
    <figcaption>
        <? if(!empty($author_id) && !bonus()): ?>
            <p id="attachmentcredit<?=$id?>" class="photocredit">
                <?= anchor('author/'.$author_id, $author_name) ?>
            </p>
        <? elseif(bonus()): ?>
            <p id="attachmentcredit<?=$id?>" class="photocredit" contenteditable="true" title="Author"><?= (!empty($author_name) ? $author_name : ''); ?></p>
        <? endif; ?>
        <p id="attachmentcaption<?=$id?>" class="photocaption" <?if(bonus()):?>contenteditable="true" title="Caption"<?endif;?>><?= (!empty($content2) ? $content2 : ''); ?></p>
    </figcaption>
    
</figure>
