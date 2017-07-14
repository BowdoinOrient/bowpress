<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    
    <article id="pagescontent">
        
        <header>
            <hgroup>
                <h2 id="pagescontenttitle" class="pagescontenttitle">Ethical Practices Policy</h2>
            </hgroup>            
        </header>
        
        <figure id="contents">
            <h3>Related links</h3>
            <ul>
                <li><?=anchor('about','About the Orient')?></li>
                <li><?=anchor('nonremoval', 'Web Non-Removal Policy')?></li>
                <li><?=anchor('comments', 'Comment Policy')?></li>
            </ul>
        </figure>
        
        <div id="pagescontentbody" class="pagescontentbody">

            <p><strong>PURPOSE:</strong> The Orient adheres to professional journalistic standards. Our success as a newspaper is dependent upon the trust of our readers and sources. This document seeks to avoid any situations that may compromise this trust.</p>
            <p><strong>SCOPE:</strong> This document applies to all Orient staffers, staff writers, columnists, and contributors. It does not apply to readers who submit an op-ed or letter to the editors, though such writers are encouraged to follow these guidelines.</p>
            <p><strong>AUTHORITY:</strong> The editors-in-chief hold sole discretion over material appearing in the Orient and are responsible for the composition of the Orient's membership. The editors-in-chief reserve the right to modify operating policies as circumstances warrant.</p>
            <ol>
                <li><strong>The Truth.</strong> Our duty is to supply truthful information to our readers. Material should be verified to the maximum extent possible. Knowingly supplying false information for publication represents a grave violation of readers' trust.</li>
                <li><strong>Original Reporting.</strong> You may never copy any material verbatim from any source without putting it in quotation marks and citing the source. We consider critical the concept of “original reporting.” Our readers rely on our reporters as first-person witnesses to the events that affect their lives. If a source is quoted or paraphrased, it is assumed that the reporter actually communicated with the source. If this is not the case, you must attribute the source of that information. For example, if you refer to a statement made by the president to the Portland Press Herald, you must indicate that the president made the statement to the Press Herald. Likewise, if you paraphrase a description written in the Brunswick Times Record, you must indicate that the information was originally reported by the Times Record. The key is making sure that it is clear to readers when you have participated in original reporting and when you have not. When the senior editorial staff determines that a writer has plagiarized, the penalty will likely include a permanent expulsion of the writer from the Orient's ranks. The Orient typically will retract the story and provide a complete explanation in its pages.</li>
                <li><strong>Professionalism.</strong> When reporting, you are a representative of the Orient and as such should conduct yourself in a professional manner. When interviewing, arrive on time, speak in a respectful manner, and be prepared with background knowledge.</li>
                <li><strong>Fairness.</strong> Individuals or organizations criticized in the preparation of a news story should have the opportunity to respond to criticism.</li>
                <li><strong>Identification.</strong> In conversations with a named source that you are planning on quoting or paraphrasing, you must identify yourself as a representative of <i>The Orient</i>. When quoting public remarks at an event that is open to the student body, you do not need to identify yourself; by definition, public remarks are public. Assignments that require anonymity in obtaining information (e.g., a restaurant review) must be approved in advance by the editors-in-chief.</li>
                <li><strong>Quotations.</strong> Material that appears in quotation marks must be the actual words used by the source. If you are not sure that you have written an exact quote in your notes, paraphrase or request clarification from the source. In accordance with New York Times policy, you may "omit extraneous syllables like 'um' and may judiciously delete false starts." We recommend that you use quotation marks in your own notes so that you may differentiate between quotations and your own paraphrasing.</li>
                <li><strong>Tape Recording.</strong> An interview may be tape recorded only with the interviewee's consent. This policy complies with the Social Code.</li>
                <li><strong>On the Record.</strong> As journalists, we always assume that statements made during an established interview are "on the record" unless a subject indicates otherwise. If an interviewee indicates that information is "off the record," be sure to clarify the stipulations. Some journalists interpret "off the record" to mean that the information can be used but not attributed; others believe that it means the information cannot be used at all.</li>
                <li>
                    <strong>Unnamed sources.</strong> While a source may ask to remain unnamed, it is always best to have named sources. If a source insists on anonymity and you wish to quote the source, we stipulate three conditions: You should indicate why the source wished to remain unnamed, you must know the source's name, and the source must know that you may share his name with the editors-in-chief for verification purposes. This follows the policy of most major newspapers, including the Washington Post:
                    <blockquote>"Sources often insist that we agree not to name them in the newspaper before they agree to talk with us. We must be reluctant to grant their wish. When we use an unnamed source, we are asking our readers to take an extra step to trust the credibility of the information we are providing. We must be certain in our own minds that the benefit to readers is worth the cost in credibility."</blockquote>
                </li>
                <li>
                    <strong>Conflicts of Interest.</strong> Conflicts of interest can cast doubt upon an entire story. Avoid them. You should not report stories that are about a close friend, an extracurricular organization that you take part in, or your professor. If you feel that you have a conflict of interest with a particular assignment, consult with your section editor.                
                    <ol style="list-style-type: lower-alpha;">
                        <li>Orient policy strictly prohibits you from writing a professor profile about an instructor who currently teaches a course you are enrolled in.</li>
                        <li>Orient policy strictly prohibits members of campus political or issue advocacy groups from reporting news or feature stories that cover issues relevant to their groups.</li>
                        <li>Orient policy currently allows members of sports teams to file reports on their teams. These writers should remember that their stories are being read by a wide audience; as such, inside jokes and nicknames should be avoided.</li>
                    </ol>
                </li>
                <li><strong>Gifts.</strong> You may not accept gifts or compensation from a story's source. Exceptions: A reasonable meal offered by a source as part of an interview, souvenir items offered to all attendees of an event (for instance, at the opening of a new building), and books and CDs sent for review. You may not receive a free meal from a restaurant in the course of conducting a restaurant review.</li>
                <li><strong>Corrections.</strong> We print nearly 30,000 words on an average week. Inevitably, some corrections will be required. If you learn of a mistake in an article, please inform your editor. It is our duty to correct or clarify erroneous information.</li>
                <li><strong>Our Independence.</strong> The Orient is editorially independent of the College and its representatives. The College and its representatives may not demand conditions regarding publication of any material.</li>
            </ol>
            
            <p><em>The ethical guidelines of the Washington Post and the New York Times have informed portions of this policy.</em></p>
        
        </div>
      
    </article>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>