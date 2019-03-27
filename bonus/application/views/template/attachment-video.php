<figure id="attachment<?php echo $id?>" data-attachment-id="<?php echo $id?>" data-attachment-type="<?php echo $type?>" data-playlist="<?php echo (!empty($playlist) ? $playlist : '')?>" class="articlemedia video-wrapper <?php echo $type?> <?php echo (!empty($playlist) ? 'playlist' : '')?> <?php echo  ($big ? 'bigphoto' : '') ?>">

    <?php if(bonus()): ?>
        <div id="deleteAttachment<?php echo $id?>" data-attachment-id="<?php echo $id?>" class="delete deleteAttachment">&times;</div>
        <div id="bigEnable<?php echo $id?>"  data-attachment-id="<?php echo $id?>" data-toggle="true"  class="bigAttachmentToggle <?php echo  ($big ? 'hide' : '') ?>">&#8689;</div>
        <div id="bigDisable<?php echo $id?>" data-attachment-id="<?php echo $id?>" data-toggle="false" class="bigAttachmentToggle <?php echo  ($big ? '' : 'hide') ?>">&#8690;</div>
    <?php endif; ?>
    
    <?php $
    $width = ($big ? '890' : '500');
    $height = floor(0.562*$width);
    ?>
    
    <div class="video-container">
    <?php if($type == 'youtube'): ?>
        <iframe width="<?php echo $width?>" height="<?php echo $height?>" src="http://www.youtube.com/embed/<?php echo $content1?>?modestbranding=1&rel=0&showinfo=0&theme=light&playlist=<?php echo (!empty($playlist) ? $playlist.',' : '')?>" frameborder="0" allowfullscreen></iframe>
    <?php elseif($type == 'vimeo'): ?>
        <iframe src="http://player.vimeo.com/video/<?php echo $content1?>?portrait=0" width="<?php echo $width?>" height="<?php echo $height?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    <?php endif; ?>
    </div>
    
    <figcaption>
        <?php if(!empty($author_id) && !bonus()): ?>
            <p id="attachmentcredit<?php echo $id?>" class="photocredit">
                <?php echo  anchor('author/'.$author_id, $author_name) ?>
            </p>
        <?php elseif(bonus()): ?>
            <p id="attachmentcredit<?php echo $id?>" class="photocredit" contenteditable="true" title="Author"><?php echo  (!empty($author_name) ? $author_name : ''); ?></p>
        <?php endif; ?>
        <p id="attachmentcaption<?php echo $id?>" class="photocaption" <?php if(bonus()):?>contenteditable="true" title="Caption"<?php endif;?>><?php echo  (!empty($content2) ? $content2 : ''); ?></p>
    </figcaption>
    
</figure>
