<? $this->load->view('template/head'); ?>

<style>
.survey {
    position: relative;
    padding-bottom: 120%;
    height: 0;
    /*overflow: hidden;*/
    overflow: scroll;
    -webkit-overflow-scrolling:touch;
}
.survey iframe{
    position: absolute;
    top:0;
    left: 0;
    width: 100%;
    height: 100%;
}
/*@media (max-device-width: 736px) {
    .survey{
        width:300px;
    }
}*/
</style>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">    
    <!-- 
    <header>
        <hgroup>
            <h2 id="articletitle" class="articletitle">This survey's response period has closed.</h2>
        </hgroup>            
    </header>
    -->
    

<!-- --> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="normalize.css">
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="sweetalert.css">
    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="sweetalert.min.js"></script>
    <script src="app.js"></script>

        <a href="#" class="header__back-link">&larr; Back to Article</a>

        <h1 class="title">2016 Bowdoin Orient Housing Quiz</h1>
        <div class="year-buttons">
            <p class="instructions">First, choose your class year.</p>

            <div class="year-buttons-wrap">
                <button class="year-button yb-2017" id="2017-button">2017</button>
                <button class="year-button yb-2018" id="2018-button">2018</button>
                <button class="year-button yb-2019" id="2019-button">2019</button>
            </div>
        </div>

        <div class="questions">
            <p class="instructions">Words words words</p>

            <div class="questions-wrap">
                <form action="#">
                    <div class="question">
                        <p class="form-guide">Being in a building with mostly people in your class.</p>
                        <div class="options">
                            <input type="radio" id="1-vi" name="1" value="vi"><label for="1-vi">Very important</label>
                            <input type="radio" id="1-si" name="1" value="si"><label for="1-si">Somewhat important</label>
                            <input type="radio" id="1-ne" name="1" value="ne"><label for="1-ne">Neutral</label>
                            <input type="radio" id="1-su" name="1" value="su"><label for="1-su">Somewhat unimportant</label>
                            <input type="radio" id="1-vu" name="1" value="vu"><label for="1-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having your bathroom cleaned.</p>
                        <div class="options">
                            <input type="radio" id="2-vi" name="2" value="vi"><label for="2-vi">Very important</label>
                            <input type="radio" id="2-si" name="2" value="si"><label for="2-si">Somewhat important</label>
                            <input type="radio" id="2-ne" name="2" value="ne"><label for="2-ne">Neutral</label>
                            <input type="radio" id="2-su" name="2" value="su"><label for="2-su">Somewhat unimportant</label>
                            <input type="radio" id="2-vu" name="2" value="vu"><label for="2-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having a kitchen.</p>
                        <div class="options">
                            <input type="radio" id="3-vi" name="3" value="vi"><label for="3-vi">Very important</label>
                            <input type="radio" id="3-si" name="3" value="si"><label for="3-si">Somewhat important</label>
                            <input type="radio" id="3-ne" name="3" value="ne"><label for="3-ne">Neutral</label>
                            <input type="radio" id="3-su" name="3" value="su"><label for="3-su">Somewhat unimportant</label>
                            <input type="radio" id="3-vu" name="3" value="vu"><label for="3-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having parking with your building.</p>
                        <div class="options">
                            <input type="radio" id="4-vi" name="4" value="vi"><label for="4-vi">Very important</label>
                            <input type="radio" id="4-si" name="4" value="si"><label for="4-si">Somewhat important</label>
                            <input type="radio" id="4-ne" name="4" value="ne"><label for="4-ne">Neutral</label>
                            <input type="radio" id="4-su" name="4" value="su"><label for="4-su">Somewhat unimportant</label>
                            <input type="radio" id="4-vu" name="4" value="vu"><label for="4-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having a quiet living community.</p>
                        <div class="options">
                            <input type="radio" id="5-vi" name="5" value="vi"><label for="5-vi">Very important</label>
                            <input type="radio" id="5-si" name="5" value="si"><label for="5-si">Somewhat important</label>
                            <input type="radio" id="5-ne" name="5" value="ne"><label for="5-ne">Neutral</label>
                            <input type="radio" id="5-su" name="5" value="su"><label for="5-su">Somewhat unimportant</label>
                            <input type="radio" id="5-vu" name="5" value="vu"><label for="5-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having a short walk to classes or to campus.</p>
                        <div class="options">
                            <input type="radio" id="6-vi" name="6" value="vi"><label for="6-vi">Very important</label>
                            <input type="radio" id="6-si" name="6" value="si"><label for="6-si">Somewhat important</label>
                            <input type="radio" id="6-ne" name="6" value="ne"><label for="6-ne">Neutral</label>
                            <input type="radio" id="6-su" name="6" value="su"><label for="6-su">Somewhat unimportant</label>
                            <input type="radio" id="6-vu" name="6" value="vu"><label for="6-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having laundry in your building.</p>
                        <div class="options">
                            <input type="radio" id="7-vi" name="7" value="vi"><label for="7-vi">Very important</label>
                            <input type="radio" id="7-si" name="7" value="si"><label for="7-si">Somewhat important</label>
                            <input type="radio" id="7-ne" name="7" value="ne"><label for="7-ne">Neutral</label>
                            <input type="radio" id="7-su" name="7" value="su"><label for="7-su">Somewhat unimportant</label>
                            <input type="radio" id="7-vu" name="7" value="vu"><label for="7-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Having a chem-free living community.</p>
                        <div class="options">
                            <input type="radio" id="8-vi" name="8" value="vi"><label for="8-vi">Very important</label>
                            <input type="radio" id="8-si" name="8" value="si"><label for="8-si">Somewhat important</label>
                            <input type="radio" id="8-ne" name="8" value="ne"><label for="8-ne">Neutral</label>
                            <input type="radio" id="8-su" name="8" value="su"><label for="8-su">Somewhat unimportant</label>
                            <input type="radio" id="8-vu" name="8" value="vu"><label for="8-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="question">
                        <p class="form-guide">Being close to athletic facilities.</p>
                        <div class="options">
                            <input type="radio" id="9-vi" name="9" value="vi"><label for="9-vi">Very important</label>
                            <input type="radio" id="9-si" name="9" value="si"><label for="9-si">Somewhat important</label>
                            <input type="radio" id="9-ne" name="9" value="ne"><label for="9-ne">Neutral</label>
                            <input type="radio" id="9-su" name="9" value="su"><label for="9-su">Somewhat unimportant</label>
                            <input type="radio" id="9-vu" name="9" value="vu"><label for="9-vu">Very unimportant</label>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>
