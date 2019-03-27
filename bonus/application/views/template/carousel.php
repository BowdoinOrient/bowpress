<figure class="articlemedia <?php if(isset($article) && $article->bigphoto){echo('bigphoto');}?>">
    <div id="swipeview_wrapper"></div>
    <div id="swipeview_relative_nav">
        <span id="prev" onclick="carousel.prev();hasInteracted=true">&laquo;</span>
        <span id="next" onclick="carousel.next();hasInteracted=true">&raquo;</span>
    </div>
    <ul id="swipeview_nav">
        <?php foreach($photos as $key => $photo): ?>
            <li <?php if($key==0): ?>class="selected"<?php endif; ?> onclick="carousel.goToPage(<?php echo $key; ?>);hasInteracted=true"></li>
        <?php endforeach; ?>
    </ul>
</figure>