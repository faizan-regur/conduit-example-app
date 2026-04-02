<html>

<head>
    <meta charset="utf-8" />
    <title>Conduit</title>
    <!-- Import Ionicon icons & Google Fonts our Bootstrap theme relies on -->
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css?family=Titillium+Web:700|Source+Serif+Pro:400,700|Merriweather+Sans:400,700|Source+Sans+Pro:400,300,600,700,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Import the custom Bootstrap 4 theme from our hosted CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
</head>
<header>
    <nav class="navbar navbar-light">
        <div class="container">
            <a class="navbar-brand" style="font-size:32px; font-weight: 600;" href="/conduit/">conduit</a>
            <ul class="nav navbar-nav pull-xs-right d-flex flex-row gap-3">
                <li class="nav-item">
                    <!-- Add "active" class when you're on that page" -->
                    <a class="nav-link active" href="/conduit/">Home</a>
                </li>
               <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    // user is logged in, show profile and logout links
                    echo '<li class="nav-item"><a class="nav-link" href="/conduit/create-article.php"> <i class="ion-compose"></i>&nbsp;New Article </a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="/conduit/settings.php"> <i class="ion-gear-a"></i>&nbsp;Settings </a></li>';
                    echo '<li class="nav-item"> <a class="nav-link" href="/conduit/profile.php"><img src="/conduit/assets/img/default-avatar.svg" class="user-pic" />Eric Simons</a></li>';
                } else {
                    // user is not logged in, show login and register links
                    echo '<li class="nav-item"><a class="nav-link" href="/conduit/login.php">Sign in</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="/conduit/register.php">Sign up</a></li>';
                }
                ?> 
            </ul>
        </div>
    </nav>
</header>


</html>