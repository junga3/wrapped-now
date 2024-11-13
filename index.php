<?php
$config = include 'resources/scripts/config.php';
$clientID = $config['clientID'];
$redirect = $config['processedURI'];

$loginURL = "https://accounts.spotify.com/authorize?client_id=${clientID}&response_type=code&redirect_uri=".$redirect."&scope=user-top-read"
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wrapped Now</title>
  <link rel="stylesheet" href="./resources/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
    rel="stylesheet">
  <script type="text/javascript" src="./resources/settings.js"></script>
</head>

<body>
  <header class="site-header">
    <div class="header-content">
      <a href="#" class="logo-link">
        <img src="resources/logo.png" alt="Logo" class="logo">
        <h2>&nbsp;Wrapped Now</h2>
      </a>
      <nav class="header-navigation">
        <a href="index.php">Home</a>
        <a href="settings.html">Settings</a>
        <a href="stats.html">Stats</a>
        <a href="privacy.html">Privacy Policy</a>
      </nav>
    </div>
  </header>
  <div class="main-content">
    <div class="wrapper">

    <div class="settings">
      <h1>Welcome!</h1>
      <p class="tagline">Wrapped Now helps showcase your top listenting history through Spotify. Through the usage of the <a href="https://developer.spotify.com/documentation/web-api">Spotify Web API</a>.</p>
      <p class="description">Click below to connect your Spotify Account and agree to our <a href="privacy.html">privacy policy</a>.</p>
      <button class="login-button" onclick="window.location.href = '<?php echo $loginURL; ?>'">Login with Spotify</button>
    </div>


  </div>
  </div>
  <footer>
    <p>Created by ITWS Team 20 <a href="https://github.com/RPI-ITWS/ITWS1100-S24-team20">Github</a></p>
  </footer>
</body>


</html>

