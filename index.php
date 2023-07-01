<?php
require_once("./lib/config.php");
session_start();
if ($_SESSION["username"] == null) {header("Location: //datalok.de/account/login?url=https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");}
$username=$_SESSION["username"];
$realname=$_SESSION["realname"];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Home | DataTube</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://files.datalok.de/Logos/datatube.webp" type="image/x-icon">
    <link rel="stylesheet" href="./lib/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body data-bs-theme="dark">
    <div class="d-flex" id="wrapper">
        <div id="sidebar" class="bg-body-tertiary">
            <img src="//files.datalok.de/Logos/datatubetext.svg" width="100%" alt="DATATUBE" class="my-3 px-2">
            <nav class="list-group list-group-flush px-3">
                <a href="./" class="btn btn-primary text-start fs-5 mb-2"><i class="fa-solid fa-play m"></i> Startseite</a>
                <a href="./trends" class="btn btn-dark text-start fs-5"><i class="fa-solid fa-fire m"></i> Trends</a>
                <hr>
                <h5>Dein Kanal:</h5>
                <a href="<?php echo("./channel?u=".$username);?>" class="btn btn-dark text-start fs-5 d-flex justify-content-start mb-2"><img src="//datalok.de/account/api?type=pf&u=<?php echo $username?>" width="32" class="rounded-5 m2"> <?php echo $username?></a>
                <a href="./studio" class="btn btn-dark text-start fs-5 mb-2"><i class="fa-solid fa-pen-to-square m"></i> Studio</a>
                <button data-bs-toggle="modal" data-bs-target="#uploadmodal" class="btn btn-dark text-start fs-5"><i class="fa-solid fa-plus m"></i> Video hochladen</button>
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
        <div id="content" class="w-100">
            <div class="sticky-top w-100 bg-body-tertiary">
                <div class="dropdown position-absolute top-0 end-0 pt-1 pe-2">
                    <button class="btn dropdown-toggle" data-bs-toggle="dropdown"><img src="//datalok.de/account/api?type=pf&u=<?php echo $username?>" width="32" class="rounded-5 m"> <?php echo $realname?></button>
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
            </div>
            <div class="row p-4 row-cols-3 col-lg-12" id="videos_row">
                <?php
                $result = mysqli_query($link, "SELECT * FROM youtube ORDER BY date DESC");
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
                                    <p class="d-flex align-content-center my-1"><img src="//datalok.de/account/api?type=pf&u='.$row["creator"].'" width="25" height="25" class="rounded-5 m"> '.$row["creator"].' • '.$datef.' • '.$row["views"].' Aufrufe</p>
                                </div>
                            </div>
                        </a>
                        ';
                        echo $articles;
                    }
                } else {echo "Keine Einträge gefunden.";}
                ?>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="uploadmodal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Erstelle dein neues Video.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="m-2" id="ajaxfeedback"></div>
                <form class="modal-body" id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="creator" value="<?php echo $username;?>">
                    <label class="form-label">Titel des Videos:</label>
                    <input class="form-control" type="text" name="title" required><br>
                    <label class="form-label">Beschreibung:</label>
                    <textarea class="form-control" type="text" name="description" style="height:150px"></textarea><hr>
                    <label class="form-label">Dein Thumbnail:</label>
                    <input name="thumbnail" class="form-control mb-1" accept=".png" type="file" id="thumbnailinput" required>
                    <img src="#" id="thumbnailpreview" width="100%" style="aspect-ratio: 16/9;"><hr>
                    <script>
                        thumbnailinput.onchange = evt => {
                            const [file] = thumbnailinput.files
                            if (file) {
                                thumbnailpreview.src = URL.createObjectURL(file)
                            }
                        }
                    </script>
                    <label class="form-label">Das eigentliche Video:</label>
                    <input name="file" accept=".mp4" class="form-control" type="file" id="fileInput" required>
                </form>
                <div class="modal-footer" id="upload_container" style="display:none;">
                    <div class="progress" style="height: 25px">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="upload_bar">0%</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="button" onclick="uploadFile()" form="uploadForm" class="btn btn-success">Video hochladen</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
function uploadFile() {
  var fileInput = document.getElementById("fileInput");
  var file = fileInput.files[0];

  var formData = new FormData(uploadForm);

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./ajax/video_upload", true);

  xhr.upload.onprogress = function (e) {
    if (e.lengthComputable) {
      var percent = Math.round((e.loaded / e.total) * 100);
      document.getElementById("upload_bar").style.width = percent+"%";
      document.getElementById("upload_bar").innerHTML = percent+"%";
    }
  };

  xhr.upload.onloadstart = function (e) {
    document.getElementById("upload_container").style.display = "block";
  };

  xhr.upload.onloadend = function (e) {
    document.getElementById("upload_bar").style.width = "99%";
    document.getElementById("upload_bar").innerHTML = 'Video verarbeiten <div class="spinner-border"></div>';
  };

  xhr.upload.onerror = function (e) {
    alert("Es ist ein Fehler beim Hochladen der Datei aufgetreten.");
  };

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
        document.getElementById("ajaxfeedback").innerHTML = this.responseText;
        msg_success();
    }
  };

  xhr.send(formData);
}

function msg_success() {
    setTimeout(() => {
        location.reload();
    }, 5000);
}
</script>
<script src="https://kit.fontawesome.com/09de98c34f.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</html>
<?php $link->close();?>