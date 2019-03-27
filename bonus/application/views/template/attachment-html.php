<figure id="attachment<?php echo $id?>" data-attachment-id="<?php echo $id?>" data-attachment-type="<?php echo $type?>" class="articlemedia html-attachment <?php echo  ($big ? 'bigphoto' : '') ?>">

    <?php if(bonus()): ?>
        <div id="deleteAttachment<?php echo $id?>" data-attachment-id="<?php echo $id?>" class="delete deleteAttachment">&times;</div>
        <div id="bigEnable<?php echo $id?>"  data-attachment-id="<?php echo $id?>" data-toggle="true"  class="bigAttachmentToggle <?php echo  ($big ? 'hide' : '') ?>">&#8689;</div>
        <div id="bigDisable<?php echo $id?>" data-attachment-id="<?php echo $id?>" data-toggle="false" class="bigAttachmentToggle <?php echo  ($big ? '' : 'hide') ?>">&#8690;</div>
    <?php endif; ?>

    <?php $width = ($big ? '890' : '500'); ?>

    <div style="max-width:<?php echo $width?>; overflow:hidden" class="html-content">
        <?php echo  $content1 ?>
    </div>

    <script>
        $wrapper = $(".html-content");
        $iframe = $(".html-content iframe");

        $iframe.load(function(){
            if($wrapper.width() < $iframe.width()){
                shrinkratio = $wrapper.width() / $iframe.width();

                // the hackiest hack
                $iframe.css("-moz-transform", "scale("+shrinkratio+")");
                $iframe.css("-moz-transform-origin", "0 0");
                $iframe.css("-o-transform", "scale("+shrinkratio+")");
                $iframe.css("-o-transform-origin", "0 0");
                $iframe.css("-webkit-transform", "scale("+shrinkratio+")");
                $iframe.css("-webkit-transform-origin", "0 0");
            }
        });
    </script>

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