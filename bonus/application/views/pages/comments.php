<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

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
                <li><?=anchor('about','About the Orient')?></li>
                <li><?=anchor('ethics', 'Ethical Practices Policy')?></li>
                <li><?=anchor('nonremoval', 'Web Non-Removal Policy')?></li>
            </ul>
        </figure>
        
        <div id="pagescontentbody" class="pagescontentbody">

            <div <?if(bonus()):?> contenteditable="true" <?endif;?> >
                <?=$content?>
            </div>
        </div>
      
    </article>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>
