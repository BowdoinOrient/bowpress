    <?php if(!empty($featured)): ?>
    <!-- FEATURED ARTICLES -->
    <section id="Featured" class="featured">
        <h2>★ Featured 
            <?php if(substr(uri_string(),0,8)=="article/" && bonus()): ?> <input type="checkbox" name="featured" value="featured" <?php if($article->featured): ?>checked="checked"<?php endif; ?> /><?php endif;?>
        </h2>
        <?php $blocktype = array(
            "blocks"=>$featured,
            "twotier"=>FALSE,
            "medtile"=>TRUE);?>
        <?php $this->load->view('template/articleblock', $blocktype);?>
    </section>
    <?php endif; ?>

    <footer id="bodyfooter">
        
        <div id="vcard" class="vcard">
            <a class="fn org url" href="http://orient.bowdoin.edu" title="The Bowdoin Orient"><span class="organization-name">The Bowdoin Orient</span></a><br>
            <span class="adr">
                <span class="email"><a href="mailto:orient@bowdoin.edu">orient@bowdoin.edu</a></span><br>
                <span class="tel"><span class="value">(207) 725-3300</span></span><br>
                <span class="street-address">6200 College Station</span><br>
                <span class="locality">Brunswick</span>, <span class="region">Maine</span> <span class="postal-code">04011</span><br>
            </span>
            <div id="copyright"><small>&copy; <?php echo date("Y")?>, The Bowdoin Orient</a>.</small></div>
        </div>
        
        <div id="footerlinks">
            <ul>
                <li><a href="<?php echo site_url()?>search">Search</a><span class="hidemobile"> <a href="<?php echo site_url()?>advsearch">(Adv)</a></span></li>
                <li><a href="<?php echo site_url()?>about">About</a></li>
                <!--<li><a href="<?php echo site_url()?>archives">Archives</a></li>-->
                <li><a href="<?php echo site_url()?>subscribe">Subscribe</a></li>
                <li><a href="<?php echo site_url()?>advertise">Advertise</a></li>
                <li><a href="<?php echo site_url()?>contact">Contact</a></li>
            </ul>
        </div>
        
    </footer>
