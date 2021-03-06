 
<?php
require("config.php");
session_start();
require("lib/login.inc");
require_once('./lib/BookGluttonEpub.php');
require_once('./lib/BookGluttonZipEpub.php');

$book_root = '/var/www/reader/books/';
$file = $book_root.$_GET['epub'];

$epub = new BookGluttonEpub();
$epub->setLogVerbose(true);
$epub->setLogLevel(2);
$epub->openLocal($file);

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
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link href="css/sticky-footer-navbar.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src='lib/annotator-full.min.js'></script>
    <link rel='stylesheet' href='lib/annotator.min.css'>
  </head>
  <body>
    <div id="wrap">
    <!-- Fixed navbar -->
      <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://reader.cali.org/">CALI Reader</a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="http://reader.cali.org/">Home</a></li>
              <li><a href="http://elangdell.cali.org/">eLangdell</a></li>
              <li><a href="http://www.cali.org/">CALI</a></li>
            </ul>
          <?php if (!$_SESSION['user']['id']) : ?>
          <ul class="nav navbar-nav navbar-right">
          <li><a href="register.php">Register</a></li>
          </ul>
          <form class="navbar-form navbar-right" method="post" action="#">
            <div class="form-group">
              <input type="text" placeholder="Username" class="form-control" name="username">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control" name="password">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
          <?php endif; ?>
          <?php if ($_SESSION['user']['id']) : ?>
          <ul class="nav navbar-nav navbar-right">
          <li class="username">Hello <?php echo $_SESSION['user']['username'] ?>.</li>  
          <li><a href="logout.php">Logout</a></li>
          </ul>
          <?php endif; ?>
        </div><!--/.nav-collapse --> 
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


echo "<ul class='nav nav-pill nav-stacked'>";

foreach($toc as $tocentry){
    $item= $tocentry['src'];
    $relpath = substr($epub->getPackagePath(), 8);
    $url = $relpath."/OEBPS/".$item;
    echo "<li class='nav-header'>Table of Contents</li>";
    echo "<li><a href='".$url."'>".$tocentry['label']."</a></li>";
    
    if(count($tocentry['navPoints']) > 0){
        $sub = $tocentry['navPoints'];
        foreach($sub as $subentry){
        $item= $subentry['src'];
        $relpath = substr($epub->getPackagePath(), 8);
        $url = $relpath."/OEBPS/".$item;
    echo "<li class=' '><a href='".$url."'>".$subentry['label']."</a>";
	
	// trying for subs
		if(count($subentry['navPoints']) > 0){
			$sub = $subentry['navPoints'];
                        echo "<ul class='nav nav-pill nav-stacked'>";
			foreach($sub as $subsubentry){
			$item= $subsubentry['src'];
			$relpath = substr($epub->getPackagePath(), 8);
			$url = $relpath."/OEBPS/".$item;
                        echo "<li class=' '><a href='".$url."'>".$subsubentry['label']."</a></li>";
			
			}
                          echo "</ul>";
		     }
	// end
	
          }
          echo "</li>";
     }       
}
echo "</ul>";
?>
        
      </div>
      <div id="bookpage" class="col-lg-8">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Thank you for previewing this eLangdell title!</h3>
            </div>
            <p class="text-warning">This is the CALI Reader and it is brand new and not completely
            finished yet. You will find that some things don't work as expected or are missing
            altogether. </p>
            <p class="text-warning">The primary feature of the CALI Reader right now is that it lets
            you read eLangdell EPUB files without having to download them to your PC or mobile device.
            This allows faculty and others to preview eLangdell titles more easily.</p>
            <p class="text-warning">To get started, click a chapter link on the left.</p>
        </div>
      </div>
      </div>  
    </div>
    </div>
        <div id="footer" class="rd-footer">
            <div class="container">
                <p class="text-muted credit"><a href="http://www.cali.org"><img alt="" src="http://www.cali.org/sites/default/files/CALI_Logo_White-footer.png" /></a>
                <a href="http://www.cali.org"> The Center for Computer-Assisted Legal Instruction</a>
                All Contents Copyright The Center for Computer-Assisted Legal Instruction</p>
      
            </div>
        </div>

    <!-- JavaScript plugins (requires jQuery) -->
    
    
    <script type="application/x-javascript">
    $("#toc").on("click", "a", function (e) {
    $("#bookpage").load($(this).attr("href"));
    e.preventDefault();
    });
    </script>
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    
    
    <script>
        $(document.body).annotator()
                        .annotator('addPlugin', 'Store', {
                                prefix: '/projects/annotator-php'
                                })
                        .annotator('addPlugin', 'Permissions', {
                                user: '<?php echo $_SESSION['user']['username']; ?>'
                                })
                        .annotator('addPlugin', 'Unsupported')
                        .annotator('addPlugin', 'Tags');

    </script>
    
    
  </body>
</html>