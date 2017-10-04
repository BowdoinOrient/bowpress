<html>
<head>

<link href='http://fonts.googleapis.com/css?family=Gloria+Hallelujah' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>

<title>Ted</title>

<style>

body {
    background-image: url(<?=base_url()?>img/ted.jpeg);
    background-size: cover;
}

#savenotify {
    position: absolute;
    margin-left: 1em;
    top: 2em;
}

p {
    margin:1em 0;
    font-family: 'Gloria Hallelujah', cursive;
}

blockquote {
    margin:1em 0;
}

blockquote p {
    margin:0; 
    font-size:1.75em;
}

.oval-thought {
    box-shadow: 2px 2px 10px #333;
    position:relative;
    width:20%;
    padding:50px 40px;
    margin:10% 0 0 70%;
    text-align:center;
    color:#000; 
    background:#eeeeee;
    /* css3 */
    background:-webkit-gradient(linear, 0 0, 0 100%, from(#eeeeee), to(#aaaaaa));
    background:-moz-linear-gradient(#eeeeee, #aaaaaa);
    background:-o-linear-gradient(#eeeeee, #aaaaaa);
    background:linear-gradient(#eeeeee, #aaaaaa);
    /*
    NOTES:
    -webkit-border-radius:220px 120px; // produces oval in safari 4 and chrome 4
    -webkit-border-radius:220px / 120px; // produces oval in chrome 4 (again!) but not supported in safari 4
    Not correct application of the current spec, therefore, using longhand to avoid future problems with webkit corrects this
    */
    -webkit-border-top-left-radius:220px 120px;
    -webkit-border-top-right-radius:220px 120px;
    -webkit-border-bottom-right-radius:220px 120px;
    -webkit-border-bottom-left-radius:220px 120px;
    -moz-border-radius:220px / 120px;
    border-radius:220px / 120px;
}

.oval-thought p {font-size:1.75em;}

/* creates the larger circle */
.oval-thought:before {
    box-shadow: 2px 2px 10px #333;
    content:"";
    position:absolute;
    bottom:-20px;
    left:50px;
    width:30px;
    height:30px;
    background:#aaa;
    /* css3 */
    -webkit-border-radius:30px;
    -moz-border-radius:30px;
    border-radius:30px;
}

/* creates the smaller circle */
.oval-thought:after {
    box-shadow: 2px 2px 10px #333;
    content:"";
    position:absolute;
    bottom:-30px;
    left:30px;
    width:15px;
    height:15px;
    background:#aaa;
    /* css3 */
    -webkit-border-radius:15px;
    -moz-border-radius:15px;
    border-radius:15px;
}

</style>

</head>

<body>

<p id="savenotify"></p>

<blockquote class="oval-thought">
    <p id="message" contenteditable="true"><?=$message?></p>
</blockquote>

<script>

$(document).ready(function()
{
    $('#message').keydown(function (e){
        if(e.keyCode == 13){
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "<?=site_url()?>ted/add",
                data: "message=" + $("#message").html(),
                success: function(result){
                    $("#savenotify").html(result);
                    $("#savenotify").show();
                    $("#savenotify").fadeOut(2000);
                    window.location.reload();
                }
            });
            
        }
    });
});

</script>

</body>

</html>