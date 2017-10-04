<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    
    <article id="pagescontent">
        
        <header>
            <hgroup>
                <h2 id="pagescontenttitle" class="pagescontenttitle">About</h2>
                <h3 id="pagescontentsubtitle" class="pagescontentsubtitle">The nation&rsquo;s oldest continuously published college weekly</h3>
            </hgroup>           
        </header>
        
        <div id="contactbox">
            <div id="vcard" class="vcard">
                <a class="fn org url" href="http://bowdoinorient.com" title="The Bowdoin Orient"><div class="organization-name">The Bowdoin Orient</div></a>
                <div class="email"><a href="mailto:orient@bowdoin.edu">orient@bowdoin.edu</a></div>
               <!-- <div class="tel"><span class="hidetablet">Telephone: </span><span class="value">(207) 725-3300</span></div>
                <div class="tel"><span class="hidetablet">Business phone: </span><span class="value">(207) 725-3053</span></div>-->
                <div class="adr">
                    <div class="street-address">6200 College Station</div>
                    <div class="locality">Brunswick</span>, <span class="region">Maine</span> <span class="postal-code">04011</div>
                </div>
                <!--<div class="staff"><span class="position">Editor in Chief:</span> <br>Linda Kinstler</div>-->
            </div>
        </div>
        
        <figure id="contents">
            <h3>Related links</h3>
            <ul>
                <li><?=anchor('ethics','Ethical Practices Policy')?></li>
                <li><?=anchor('nonremoval', 'Web Non-Removal Policy')?></li>
                <li><?=anchor('comments', 'Comment Policy')?></li>
            </ul>
        </figure>
        
        <div id="pagescontentbody" class="pagescontentbody">

            <p>The Bowdoin Orient is a student-run weekly publication dedicated to providing news and information relevant to the College community. Editorially independent of the College and its administrators, the Orient pursues such content freely and thoroughly, following professional journalistic standards in writing and reporting. The Orient is committed to serving as an open forum for thoughtful and diverse discussion and debate on issues of interest to the College community.</p>

            <p>The paper has an on-campus distribution of over 1,500, and is mailed off-campus to several hundred subscribers. It is distributed free-of-charge at various locations on campus, including the student union, several classroom buildings, the admissions office, the dining halls, and the libraries. Published on Fridays, 24 times a year, the Orient is read by students and their families, faculty, staff, alumni, off-campus subscribers, prospective students, and campus visitors.</p>
                        
            <h3>Staff</h3>
            <?//Pulling in the masthead?>
            <?=$content?>
            
            <div style="clear:both;"></div>
            
            <h3>History</h3>
            <p>The Bowdoin Orient was established in 1871 as Bowdoin College's newspaper and literary magazine. Originally issued bi-weekly, it has been a weekly since April, 1899. It is considered to be the oldest continuously-published college weekly in the U.S., which means that it has been in publication every academic year that Bowdoin has been in session since it began publishing weekly (while other college weeklies stopped printing during certain war years).</p>
            <p>In the beginning, the Orient was laid out in a smaller magazine format, and included literary material such as poems and fiction alongside its news. In 1897, the literary society formed its own publication, The Quill, and the Orient has since primarily focused on reporting news. In 1921, the Orient moved from the magazine format to a larger broadsheet layout and has changed between broadsheet and tabloid sizes frequently.</p>
            <p>In 1912, The Bowdoin Publishing Company was established as the formal publisher of the Orient, and remained independent of the College for many years while using college facilities and working with faculty-member advisers. The Bowdoin Publishing Company was a legal, non-profit corporation in the State of Maine for many years (at least from 1968 to 1989), though it was most likely an independent corporation since its inception. In recent years, the Orient has printed with the presses of the <a href="http://www.timesrecord.com/">Brunswick Times Record</a>.</p>
            <p>The Orient building has its own archives, with issues dating back to 1873, but it is missing several periods of time. The Hawthorne-Longfellow Library has a nearly complete archive of past Orient issues, both in print and on microform. Almost all print issues are available from 1871 to the present in Special Collections and Archives. Bound copies from 1871 to 1921 can be found in the periodicals section of the library. Microform is available for issues 1921 to the present, and can be accessed at any time.</p>
        
            <h3>Website</h3>
            <p>The Orient website went online around 2000. It was redesigned in 2001, 2004 (becoming database-driven), 2009 (with a visual overhaul), and 2012 (rewritten from scratch). The most recent version is a responsive design intended to work across all devices, including phones and tablets.</p>
            <p>Built by Vic Kotecha &rsquo;05, James Baumberger &rsquo;06, Mark Hendrickson &rsquo;07, Seth Glickman &rsquo;10, Toph Tucker &rsquo;12, Brian Jacobel &rsquo;14, and Andrew Daniels &rsquo;15 in Brunswick, Rome, and Chestnut Hill. Written in PHP (on the CodeIgniter framework) and JavaScript (with jQuery) with a MySQL database running on Apache. To get involved, <a href="mailto:andrew.daniels714@gmail.com">email Andrew</a>.</p>
            <p>In October 2009, the Orient began <a href="https://twitter.com/bowdoinorient">tweeting</a>. In September 2010, the <a href="http://bowdoinorientexpress.com/">Orient Express</a> companion Tumblr launched to provide content beyond the scope, reach, and technical capabilities of the printed Friday paper. It was retired in the fall of 2013.</p>
            <p>Bowdoinorient.com requires a modern browser. If things look wrong, <a href="http://www.whatbrowser.org/">please upgrade</a>. We recommend a WebKit-based browser like <a href="http://www.google.com/intl/en/chrome/">Chrome</a> or <a href="http://apple.com/safari">Safari</a>.</p>
            <!-- Other browser ballots include WordPress's http://browsehappy.com/ and Microsoft's http://browserchoice.eu/ -->
            
        </div>
      
    </article>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>
