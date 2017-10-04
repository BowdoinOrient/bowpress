<figure id="photo<?=$photo->photo_id?>" data-photo-id="<?=$photo->photo_id?>" data-attachment-type="photo" class="articlemedia photo-wrapper <?= ($article->bigphoto ? 'bigphoto' : '') ?>">

    <? if(bonus()): ?>
        <div id="deletePhoto<?=$photo->photo_id?>"     data-photo-id="<?=$photo->photo_id?>" class="delete deletePhoto">&times;</div>
        <div id="bigPhotoEnable<?=$photo->photo_id?>"  data-photo-id="<?=$photo->photo_id?>" data-toggle="true"  class="bigPhotoToggle <?= ($article->bigphoto ? 'hide' : '') ?>">&#8689;</div>
        <div id="bigPhotoDisable<?=$photo->photo_id?>" data-photo-id="<?=$photo->photo_id?>" data-toggle="false" class="bigPhotoToggle <?= ($article->bigphoto ? '' : 'hide') ?>">&#8690;</div>
    <? endif; ?>
    
    <img src="<?=base_url()?>images/<?=$article->date?>/<?=$photo->filename_large?>" class="articlephoto">
    
    <figcaption>
        <? if(!empty($photo->photographer_id)): ?>
            <?if(bonus()):?>
                <p id="photocredit<?=$photo->photo_id?>" class="photocredit" contenteditable="true" title="Photographer"><?= $photo->photographer_name; ?></p>
            <?else:?>
                <p id="photocredit<?=$photo->photo_id?>" class="photocredit">
                    <?= anchor('author/'.$photo->photographer_id, $photo->photographer_name) ?>
                </p>
            <?endif;?>
        <? elseif(empty($photo->credit) && bonus()): ?>
            <p id="photocredit<?=$photo->photo_id?>" class="photocredit" contenteditable="true" title="Photographer"></p>
        <? else: ?>
            <p id="photocredit<?=$photo->photo_id?>" class="photocredit">
                <?= $photo->credit ?>
            </p>
        <? endif; ?>
        <p id="photocaption<?=$photo->photo_id?>" class="photocaption" <?if(bonus()):?>contenteditable="true" title="Caption"<?endif;?>><?=$photo->caption?></p>
    </figcaption>
    
</figure>