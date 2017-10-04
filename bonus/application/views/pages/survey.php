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
    
    <div class="survey">
        <iframe id="survey"src="https://docs.google.com/forms/d/1RDDBLjcEZLsm2hxh5xU-FRLgnn2Yz-BSMW7SAPGwypk/viewform?embedded=true#start=openform" width="760" height="500" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
        <!-- <iframe id="survey" src="https://docs.google.com/forms/d/1LaKxhjXkybhjv3F6dqk0LYE18HxVSscOxhpDTHIqzeU/viewform?embedded=true" height="500" width="760" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe> -->
    </div>
    <script>
        /* Set the iframe to a reasonable height so that most of the scrolling 
            is in the frame*/
        // document.getElementById("survey").height = window.screen.height - 47 - 63- 45;
    </script>
</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>
