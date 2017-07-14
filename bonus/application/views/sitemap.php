<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

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
   
<? foreach($articles as $article): ?>

   <url>
      <loc>http://bowdoinorient.com/article/<?=$article->id?></loc>
   </url>

<? endforeach; ?>

<? foreach($authors as $author): ?>

   <url>
      <loc>http://bowdoinorient.com/author/<?=$author->id?></loc>
   </url>

<? endforeach; ?>

<? foreach($series as $serie): ?>

   <url>
      <loc>http://bowdoinorient.com/series/<?=$serie->id?></loc>
   </url>

<? endforeach; ?>

</urlset>