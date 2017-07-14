<figure class="articlemedia <?if(isset($article) && $article->bigphoto){echo('bigphoto');}?>">
    <div id="swipeview_wrapper"></div>
    <div id="swipeview_relative_nav">
        <span id="prev" onclick="carousel.prev();hasInteracted=true">&laquo;</span>
        <span id="next" onclick="carousel.next();hasInteracted=true">&raquo;</span>
    </div>
    <ul id="swipeview_nav">
        <? foreach($photos as $key => $photo): ?>
            <li <? if($key==0): ?>class="selected"<? endif; ?> onclick="carousel.goToPage(<?=$key; ?>);hasInteracted=true"></li>
        <? endforeach; ?>
    </ul>
</figure>