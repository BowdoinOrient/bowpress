<?php $this->load->view('template/head'); ?>

<body>

<?php $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    
    <article id="pagescontent">
        
        <header>
            <hgroup>
                <h2 id="pagescontenttitle" class="pagescontenttitle">Comment Policy</h2>
            </hgroup>            
        </header>
        
        <figure id="contents">
            <h3>Related links</h3>
            <ul>
                <li><?php echo anchor('about','About the Orient')?></li>
                <li><?php echo anchor('ethics', 'Ethical Practices Policy')?></li>
                <li><?php echo anchor('nonremoval', 'Web Non-Removal Policy')?></li>
            </ul>
        </figure>
        
        <div id="pagescontentbody" class="pagescontentbody">

            <div <?php if(bonus()):?> contenteditable="true" <?php endif;?> >
                <?php echo $content?>
            </div>
        </div>
      
    </article>

</div>

<?php $this->load->view('template/bodyfooter', $footerdata); ?>

<?php $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>
