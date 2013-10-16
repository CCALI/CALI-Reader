<?php 
    require("config.php");
    if(!empty($_POST)) 
    { 
        // Ensure that the user fills out fields 
        if(empty($_POST['username'])) 
        { die("Please enter a username."); } 
        if(empty($_POST['password'])) 
        { die("Please enter a password."); } 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { die("Invalid E-Mail Address"); } 
         
        // Check if the username is already taken
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                username = :username 
        "; 
        $query_params = array( ':username' => $_POST['username'] ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 
        if($row){ die("This username is already in use"); } 
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        "; 
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage());} 
        $row = $stmt->fetch(); 
        if($row){ die("This email address is already registered"); } 
         
        // Add row to database 
        $query = " 
            INSERT INTO users ( 
                username, 
                password, 
                salt, 
                email 
            ) VALUES ( 
                :username, 
                :password, 
                :salt, 
                :email 
            ) 
        "; 
         
        // Security measures
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
        $password = hash('sha256', $_POST['password'] . $salt); 
        for($round = 0; $round < 65536; $round++){ $password = hash('sha256', $password . $salt); } 
        $query_params = array( 
            ':username' => $_POST['username'], 
            ':password' => $password, 
            ':salt' => $salt, 
            ':email' => $_POST['email'] 
        ); 
        try {  
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        header("Location: index.php"); 
        die("Redirecting to index.php"); 
    } 
?>
<!-- Author: Michael Milstead / Mode87.com
     for Untame.net
     Bootstrap Tutorial, 2013
-->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register for CALI Reader</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link href="css/sticky-footer-navbar.css" rel="stylesheet">
    
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

        <div class="container hero-unit">
    
    <h1>Register</h1> <br /><br />
    <form action="register.php" method="post"> 
        <label>Username:<strong style="color:darkred;">*</strong></label> 
        <input type="text" name="username" value="" /> 
        <label>Email: <strong style="color:darkred;">*</strong></label> 
        <input type="text" name="email" value="" /> 
        <label>Password:<strong style="color:darkred;">*</strong></label> 
        <input type="password" name="password" value="" /> <br /><br />
        <p style="color:darkred;">* All fields required</p><br />
        <input type="submit" class="btn btn-info" value="Register" /> 
    </form>
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
    <script src="http://code.jquery.com/jquery.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
</body>
</html>