<?php echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0">'?> 
<?php
function xmlclean($string) {
    return str_replace(array("&", "<", ">", "\"", "'"),
        array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), $string);
}
?>
  <channel>
    <title><?php echo  xmlclean($title) ?></title>
    <link><?php echo  $url ?></link>
    <description><?php echo  xmlclean($description) ?></description>
    <?php foreach($articles as $article): ?>
    <item>
       <guid>http://bowdoinorient.com/article/<?php echo  $article->id ?></guid>
       <title><?php echo  xmlclean($article->title) ?></title>
       <link>http://bowdoinorient.com/article/<?php echo  $article->id ?></link>
       <pubDate><?php echo  gmdate(DATE_RSS, strtotime($article->date)); ?></pubDate>
       <description><?php echo  xmlclean($article->excerpt) ?></description>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>