<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    
    <article id="pagescontent">
        
        <header>
            <hgroup>
                <h2 id="pagescontenttitle" class="pagescontenttitle">Web Non-Removal Policy</h2>
            </hgroup>            
        </header>
        
        <figure id="contents">
            <h3>Related links</h3>
            <ul>
                <li><?=anchor('about','About the Orient')?></li>
                <li><?=anchor('ethics', 'Ethical Practices Policy')?></li>
                <li><?=anchor('comments', 'Comment Policy')?></li>
            </ul>
        </figure>
        
        <div id="pagescontentbody" class="pagescontentbody">

            <p>In recent years, The Orient has received requests for material to be removed from the web-based version of the newspaper. Specifically, some alumni have objected to the archiving of material that was published in The Orient, since the material is readily accessible through the website's search engine and search engines like Google.</p>
            <p>The editors, after consulting more than a half-dozen professional journalists and journalism scholars, have determined that all requests for material alteration or removal will be declined.</p>
            <p>This policy has been created under the ethical premise that history should not be revised to fit private interests. Alteration or removal of material from The Orient's online archive would be done solely for the interest of a single or small number of individuals. Except in extraordinary circumstances, journalists believe that the public is best served by the uninterrupted and free flow of information.</p>
            <p>Further considerations:</p>
            <ul>
                <li>The College and the editors of The Orient have archived the physical newspaper since its conception in 1871. A complete physical archive is publicly accessible in the Bowdoin College Library. The web version of The Orient simply provides a new publication medium for new times.</li>
                <li>The removal or alteration of specific online articles presents a slippery slope that would require the editors to make subjective decisions about what alteration requests should be permitted. For example, it is not inconceivable that a public figure would request that statements made in an opinion submission or quotations published in a news article be expunged or altered.</li>
                <li>Were The Orient to comply with requests that ask for the alteration of material that was written by someone other than the requestor, writers who submitted content or were referenced in published content would be silenced without their knowledge.</li>
                <li>Items were posted on the web at the same time as print publication. Thus, at the time of publication, the newspaper's position as a public forum included the web-based dissemination of content.</li>
            </ul>
            <p>Should a discussion of Orient content arise in a job interview situation, the editors would advise any former contributors to the Orient to explain how and why their views have changed, if that is the case.</p>
            <!--<p><em>October 2011</em></p>-->
        
        </div>
      
    </article>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>