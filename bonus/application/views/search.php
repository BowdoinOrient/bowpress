<? $this->load->view('template/head'); ?>

<body>

<? $this->load->view('template/bodyheader', $headerdata); ?>

<div id="content">
    
    <article id="mainstory">
        
        <header>
            <hgroup>
                <h2 id="articletitle" class="articletitle searchtitle">Search</h2>
                <!--<h3 id="articlesubtitle" class="articlesubtitle"></h3>-->
            </hgroup>            
        </header>
        
        <div id="bigsearch">
            <form action="<?=site_url()?>search" id="cse-search-box" method="get">
                <input class="filterinput" type="text" value="<?=$query?>" name="q" autofocus>
            </form>
        </div>
        
        <div id="articlebody" class="articlebody">
        
            <div id="cse" style="width: 100%;"></div>
            <script src="http://www.google.com/jsapi" type="text/javascript"></script>
            <script type="text/javascript"> 
              google.load('search', '1', {language : 'en', style : google.loader.themes.V2_DEFAULT});
              google.setOnLoadCallback(function() {
                var customSearchOptions = {};  var customSearchControl = new google.search.CustomSearchControl(
                  '013877420925418038085:0ibijs0mmig', customSearchOptions);
                customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
                var options = new google.search.DrawOptions();
                options.enableSearchResultsOnly(); 
                customSearchControl.draw('cse', options);
                function parseParamsFromUrl() {
                  var params = {};
                  var parts = window.location.search.substr(1).split('\x26');
                  for (var i = 0; i < parts.length; i++) {
                    var keyValuePair = parts[i].split('=');
                    var key = decodeURIComponent(keyValuePair[0]);
                    params[key] = keyValuePair[1] ?
                        decodeURIComponent(keyValuePair[1].replace(/\+/g, ' ')) :
                        keyValuePair[1];
                  }
                  return params;
                }
            
                var urlParams = parseParamsFromUrl();
                var queryParamName = "q";
                if (urlParams[queryParamName]) {
                  customSearchControl.execute(urlParams[queryParamName]);
                }
              }, true);
            </script>
            
            <p><?=anchor('advsearch','Advanced search')?></p>
             
        </div>
      
    </article>

</div>

<? $this->load->view('template/bodyfooter', $footerdata); ?>

<? $this->load->view('bonus/bonusbar', TRUE); ?>

</body>

</html>