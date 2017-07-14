<div class="attachment scribd" data-afterpar="<?=$afterpar?>">
    <? $json = json_decode(file_get_contents("http://www.scribd.com/services/oembed/?format=json&url=http://www.scribd.com/doc/".$content1), true);?>
    <?=$json["html"]?>
</div>