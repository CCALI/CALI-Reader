 
<?php
require_once('./lib/BookGluttonEpub.php');
require_once('./lib/BookGluttonZipEpub.php');

$book_root = '/var/www/reader/library/';
$file = $book_root.$_GET['epub'];

$epub = new BookGluttonEpub();
$epub->setLogVerbose(true);
$epub->setLogLevel(2);
$epub->open($file);

$metadata = $epub->getMetaPairs();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>CALI Reader Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css">
    <link href="css/sticky-footer-navbar.css" rel="stylesheet">
  </head>
  <body>
    <div id="wrap">
          <!-- Fixed navbar -->
      <div class="navbar navbar-fixed-top">
        <div class="container">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">CALI Reader</a>
          <div class="nav-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
      <div class="container">

<?php
echo "<div class='page-header'>";
echo "<h1>".$metadata['dc:title']."</h1>";
echo "<h3>".$metadata['dc:creator']."</h3>";
echo "<h4>".$metadata['dc:publisher']."</h4>";
echo "</div>";
?>
    <div class="row">
      <div id="toc" class="col-lg-4">
<?php
$toc = $epub->getNavPoints();


echo "<ul>";
foreach($toc as $tocentry){
    $item= $tocentry['src'];
    $relpath = substr($epub->getPackagePath(), 8);
    $url = $relpath."/OEBPS/".$item;
    echo "<li><a href='".$url."' class='chapter'>".$tocentry['label']."</a></li>";
    
    if(count($tocentry['navPoints']) > 0){
        $sub = $tocentry['navPoints'];
        foreach($sub as $subentry){
        $item= $subentry['src'];
        $relpath = substr($epub->getPackagePath(), 8);
        $url = $relpath."/OEBPS/".$item;
    echo "<li><a href='".$url."' class='chapter'>".$subentry['label']."</a></li>";
	
	// trying for subs
		if(count($subentry['navPoints']) > 0){
			$sub = $subentry['navPoints'];
                        echo "<ul>";
			foreach($sub as $subsubentry){
			$item= $subsubentry['src'];
			$relpath = substr($epub->getPackagePath(), 8);
			$url = $relpath."/OEBPS/".$item;
                        echo "<li><a href='".$url."' class='chapter'>".$subsubentry['label']."</a></li>";
			
			}
                          echo "</ul>";
		     }
	// end
	
          } 
     }       
}
echo "</ul>";
?>
      </div>
      <div id="bookpage" class="col-lg-8">8</div>
      </div>  
    </div>
    </div>
       <div id="footer">
      <div class="container">
        <p class="text-muted credit"><a href="http://www.cali.org">CALI</a></p>
      </div>
    </div>


    <!-- JavaScript plugins (requires jQuery) -->
    <script src="http://code.jquery.com/jquery.js"></script>
    
    <script type="application/x-javascript">
    $("#toc").on("click", "a", function (e) {
    $("#bookpage").load($(this).attr("href"));
    e.preventDefault();
    });
    
    </script>
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/js/bootstrap.min.js"></script>

    
  </body>
</html>