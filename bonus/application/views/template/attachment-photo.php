<figure id="photo<?php echo $photo->photo_id?>" data-photo-id="<?php echo $photo->photo_id?>" data-attachment-type="photo" class="articlemedia photo-wrapper <?php echo  ($article->bigphoto ? 'bigphoto' : '') ?>">

    <?php if(bonus()): ?>
        <div id="deletePhoto<?php echo $photo->photo_id?>"     data-photo-id="<?php echo $photo->photo_id?>" class="delete deletePhoto">&times;</div>
        <div id="bigPhotoEnable<?php echo $photo->photo_id?>"  data-photo-id="<?php echo $photo->photo_id?>" data-toggle="true"  class="bigPhotoToggle <?php echo  ($article->bigphoto ? 'hide' : '') ?>">&#8689;</div>
        <div id="bigPhotoDisable<?php echo $photo->photo_id?>" data-photo-id="<?php echo $photo->photo_id?>" data-toggle="false" class="bigPhotoToggle <?php echo  ($article->bigphoto ? '' : 'hide') ?>">&#8690;</div>
    <?php endif; ?>
    
    <img src="<?php echo base_url()?>images/<?php echo $article->date?>/<?php echo $photo->filename_large?>" class="articlephoto">
    
    <figcaption>
        <?php if(!empty($photo->photographer_id)): ?>
            <?php if(bonus()):?>
                <p id="photocredit<?php echo $photo->photo_id?>" class="photocredit" contenteditable="true" title="Photographer"><?php echo  $photo->photographer_name; ?></p>
            <?php else:?>
                <p id="photocredit<?php echo $photo->photo_id?>" class="photocredit">
                    <?php echo  anchor('author/'.$photo->photographer_id, $photo->photographer_name) ?>
                </p>
            <?php endif;?>
        <?php elseif(empty($photo->credit) && bonus()): ?>
            <p id="photocredit<?php echo $photo->photo_id?>" class="photocredit" contenteditable="true" title="Photographer"></p>
        <?php else: ?>
            <p id="photocredit<?php echo $photo->photo_id?>" class="photocredit">
                <?php echo  $photo->credit ?>
            </p>
        <?php endif; ?>
        <p id="photocaption<?php echo $photo->photo_id?>" class="photocaption" <?php if(bonus()):?>contenteditable="true" title="Caption"<?php endif;?>><?php echo $photo->caption?></p>
    </figcaption>
    
</figure>