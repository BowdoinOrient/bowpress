<div class="attachment video" data-afterpar="<?php echo $afterpar?>">
    <?php 
        if ($type == 'vimeo') {
            $json = json_decode(file_get_contents("http://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/".$content1), true);
        } elseif ($type == 'youtube') {
            $json = json_decode(file_get_contents("http://www.youtube.com/oembed?url=http%3A//www.youtube.com/watch?v%3D".$content1."&format=json"), true);
        } 
        
        $embed = new stdClass();
        
        preg_match("/height=\"(\d+)\"/", $json["html"], $matches);
        $embed->height = $matches[1];
        
        preg_match("/width=\"(\d+)\"/", $json["html"], $matches);
        $embed->width = $matches[1];
        
        preg_match("/src=\"([^\"]+)\"/", $json["html"], $matches);
        $embed->src = $matches[1];
        
        if ($json["provider_name"] == "YouTube") {
            $embed->src = $embed->src."&html5=1";
        }
    ?>
    <iframe width="<?php echo $embed->width?>" height="<?php echo $embed->height?>" src="<?php echo $embed->src?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen data-afterpar="<?php echo $afterpar?>"></iframe>
</div>
<script>
    $vid = $(".video iframe").filter("[data-afterpar='<?php echo $afterpar?>']");
    $aspectratio = $vid.height() / $vid.width();
    $vid.css("width", "100%");
    $vid.css("height", $vid.width() * $aspectratio);
</script>