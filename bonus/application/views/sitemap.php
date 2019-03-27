<?php echo  '<?xml version="1.0" encoding="UTF-8"?>' ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

   <url>
      <loc>http://bowdoinorient.com/</loc>
   </url>
   
   <url>
      <loc>http://bowdoinorient.com/about</loc>
   </url>
   
   <url>
      <loc>http://bowdoinorient.com/subscribe</loc>
   </url>
   
   <url>
      <loc>http://bowdoinorient.com/advertise</loc>
   </url>
   
   <url>
      <loc>http://bowdoinorient.com/contact</loc>
   </url>
   
   <url>
      <loc>http://bowdoinorient.com/search</loc>
   </url>
   
<?php foreach($articles as $article): ?>

   <url>
      <loc>http://bowdoinorient.com/article/<?php echo $article->id?></loc>
   </url>

<?php endforeach; ?>

<?php foreach($authors as $author): ?>

   <url>
      <loc>http://bowdoinorient.com/author/<?php echo $author->id?></loc>
   </url>

<?php endforeach; ?>

<?php foreach($series as $serie): ?>

   <url>
      <loc>http://bowdoinorient.com/series/<?php echo $serie->id?></loc>
   </url>

<?php endforeach; ?>

</urlset>