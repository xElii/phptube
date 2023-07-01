<?php
require_once("./lib/config.php");
session_start();
$username=$_SESSION["username"];
$realname=$_SESSION["realname"];

$channel=$_GET["u"] ?? null;
if ($channel==null) {$msg='<p class="alert alert-danger">Diesen Channel gibt es nicht!</p>';};





?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Channel | DataTube</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://files.datalok.de/Logos/datatube.webp" type="image/x-icon">
    <link rel="stylesheet" href="./lib/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body data-bs-theme="dark">
    <div class="d-flex" id="wrapper">
        <div id="sidebar" class="bg-body-tertiary offcanvas offcanvas-start">
            <img src="//files.datalok.de/Logos/datatubetext.svg" width="100%" alt="DATATUBE" class="my-3 px-2">
            <nav class="list-group list-group-flush px-3">
                <a href="./" class="btn btn-dark text-start fs-5 mb-2"><i class="fa-solid fa-play m"></i> Startseite</a>
                <a href="./trends" class="btn btn-dark text-start fs-5"><i class="fa-solid fa-fire m"></i> Trends</a>
                <hr>
                <h5>Dein Kanal:</h5>
                <a href="<?php echo("./channel?u=".$username);?>" class="btn btn-dark text-start fs-5 d-flex justify-content-start mb-2"><img src="//datalok.de/account/api?type=pf&u=<?php echo $username?>" width="32" class="rounded-5 m2"> <?php echo $username?></a>
                <a href="./studio" class="btn btn-dark text-start fs-5 mb-2"><i class="fa-solid fa-pen-to-square m"></i> Studio</a>
                <hr>
                <h5>Andere Kanäle:</h5>
                <?php
                $result = mysqli_query($link, "SELECT * FROM Accounts WHERE username!='$username'");
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $unixtime=$row["date"];
                        $datef=date('j. M Y H:i', $unixtime);
                        $userlist='<a href="./channel?u='.$row['username'].'" class="btn btn-dark text-start fs-5 d-flex justify-content-start my-1"><img src="//datalok.de/account/api?type=pf&u='.$row['username'].'" width="32" class="rounded-5 m2"> '.$row['username'].'</a>';
                        echo $userlist;
                    }
                }
                ?>
                </nav>
        </div>
            <div class="sticky-top w-100 bg-body-tertiary">
                <div class="dropdown position-absolute top-0 end-0 pt-1 pe-2">
                    <button class="btn dropdown-toggle" data-bs-toggle="dropdown"><img src="//datalok.de/account/api?type=pf&u=<?php echo $username?>" width="32" class="rounded-5 m"> <?php echo ('<span class="profilename">'.$realname.'</span>')?></button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="//datalok.de/account"><i class="fa-solid fa-gear m"></i> Einstellungen</a></li>
                        <?php if ($username=="ekoeppl") {echo('<li><a class="dropdown-item" href="./admin" class="btn"><i class="fa-solid fa-wrench m"></i> Admin</a></li>');}?>
                    </ul>
                </div>
                <form action="./search" class="d-flex py-2" id="searchbar">
                    <div class="w-50" style="display:flex; align-items:center;">
                        <input name="s" class="form-control" style="padding-left: 35px;" type="search">
                        <i class="fa fa-magnifying-glass searchicon"></i>
                    </div>
                </form>
                <div class="position-absolute top-0 pt-2 ps-1">
                    <a class="btn" data-bs-toggle="offcanvas" href="#sidebar" role="button" aria-controls="sidebar"><i class="fa-solid fa-bars"></i></a>
                    <a href="./"><img src="https://files.datalok.de/Logos/datatube.webp" width="32"></a>
                </div>
            </div>
        </div>
        <section class="container mt-3">
            <div class="card bg-body-secondary bg-gradient mb-3">
                <div class="card-body text-center">
                    <img src="../account/api?type=pf&u=<?php echo $channel?>" width="180"><h1><?php echo $channel?></h1>
                </div>
            </div>
            <div class="row row-cols-3 col-lg-12" id="videos_row">
                <?php
                $result = mysqli_query($link, "SELECT * FROM youtube WHERE creator='$channel' ORDER BY date DESC");
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $unixtime=$row["date"];
                        $datef=date('j. M Y H:i', $unixtime);
                        $articles='
                        <a href="./watch?v='.$row["id"].'" class="col mb-3 videocard" title="'.$row["title"].'">
                            <div class="card">
                                <img src="./videos/'.$row["id"].'.png" class="card-img-top" style="aspect-ratio: 16 / 9;">
                                <div class="card-body">
                                    <h3 class="videotitle">'.$row["title"].'</h3><hr>
                                    <p class="d-flex align-content-center my-1">'.$datef.' • '.$row["views"].' Aufrufe • '.$row["likes"].' Likes</p>
                                </div>
                            </div>
                        </a>
                        ';
                        echo $articles;
                    }
                } else {$msg='<h2>Dieser Kanal hat noch keine Videos!</h2>';}
                ?>
            </div>
            <?php echo $msg?>
        </section>
</body>
<script src="https://kit.fontawesome.com/09de98c34f.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</html>
<?php $link->close();?>