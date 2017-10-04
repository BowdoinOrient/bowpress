<figure id="attachment<?=$id?>" data-attachment-id="<?=$id?>" data-attachment-type="<?=$type?>" class="articlemedia html-attachment <?= ($big ? 'bigphoto' : '') ?>">

    <? if(bonus()): ?>
        <div id="deleteAttachment<?=$id?>" data-attachment-id="<?=$id?>" class="delete deleteAttachment">&times;</div>
        <div id="bigEnable<?=$id?>"  data-attachment-id="<?=$id?>" data-toggle="true"  class="bigAttachmentToggle <?= ($big ? 'hide' : '') ?>">&#8689;</div>
        <div id="bigDisable<?=$id?>" data-attachment-id="<?=$id?>" data-toggle="false" class="bigAttachmentToggle <?= ($big ? '' : 'hide') ?>">&#8690;</div>
    <? endif; ?>

    <? $width = ($big ? '890' : '500'); ?>

    <div style="max-width:<?=$width?>; overflow:hidden" class="html-content">
        <?= $content1 ?>
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