<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">

    <article id="pagescontent">

        <header>
            <hgroup>
                <h2 id="pagescontenttitle" class="pagescontenttitle">Contact us</h2>
            </hgroup>
        </header>

        <img src="<?=base_url()?>img/connect-please.jpg" align="right" title="Katie Fitch, The Bowdoin Orient" id="connect-please" class="hidemobile">

        <div id="pagescontentbody" class="pagescontentbody">
              <p>The Bowdoin Orient<br/>
                    6200 College Station<br/>
                    Brunswick, ME 04011-8462</p>

                    <!--<p>Phone: (207) 725-3300<br/>
                    Business Phone: (207) 725-3053</p>-->

                    <p>General inquiries and subscriptions: <a href="mailto:orient@bowdoin.edu">orient@bowdoin.edu</a>.</p>

                    <p>Business or advertising inquiries: <a href="mailto:orientads@bowdoin.edu">orientads@bowdoin.edu</a>.</p>

                    <p>Website issues: <a href="mailto:orientwebmaster@gmail.com">orientwebmaster@gmail.com</a>.</p>

            <h4>We welcome submissions from the entire Bowdoin community and beyond.</h4>

            <h3 id="ltte">Submit a letter to the editor</h3>
            <ul>
                <li>Letters must be signed. We will not publish unsigned letters.</li>
                <li>Letters should not exceed 200 words.</li>
                <!--<li>Op-eds run from 500 to 700 words, if you wish to submit a longer piece.</li>-->
            </ul>
            <h3 id="ltte">Submit an Op-Ed</h3>
            <ul>
                <li>Op-eds express an opinion about an issue within the Bowdoin Bubble or beyond.</li>
                <li>Op-eds should be between 500 and 700 words.</li>
            </ul>
            <h3 id="ltte">Submit a Talk of the Quad</h3>
            <ul>
                <li> A Talk of the Quad expresses a personal narrative.</li>
                <li>A Talk of the Quad should be between 600 and 800 words.</li>
                <li>For examples, see <a href="http://bowdoinorient.com/series/114">Talk of the Quad</a>.</li>
            </ul>

            <p>All submissions should:
            <ul>
                <li>include your full name and a phone number.</li>
                <li>be recieved by 7 p.m. on the Wednesday of the week of publication.</li>
                <li>be sent to <a href="mailto:orientopinion@bowdoin.edu">orientopinion@bowdoin.edu</a>.</li>
            </ul>
            </p>
            <h3 id="ltte">Join the Orient</h3>
            <ul>
                <li>The Orient open to all students and welcomes interested reporters, photographers, programmers and illustrators.</li>
                <li>To join, contact Meg Robbins or Julian Andrews</li>
            </ul>
        </div>

    </article>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>
