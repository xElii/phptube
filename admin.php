<?php
require_once("./lib/config.php");
session_start();
if ($_SESSION["username"]==null) {header("Location: //datalok.de/account/login?url=https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");}
if ($_SESSION["username"]!='amdin') {header("Location: ./");}
$username=$_SESSION["username"];
$realname=$_SESSION["realname"];

$edit=$_GET["edit"] ?? null;

// Edit Video
if ($edit!=null && $_SERVER["REQUEST_METHOD"]=="POST") {
    $title_form=$_POST["title"];
    $desc_form=$_POST["description"];
    $sql = "UPDATE youtube SET title='$title_form',description='$desc_form' WHERE id='$edit'";
    if ($link->query($sql) === TRUE) {}
}

// Delete Video
$delete=$_GET["del"] ?? null;
if ($delete!=null) {
    $sql = "DELETE FROM youtube WHERE id='$delete'";
    if ($link->query($sql) === TRUE) {
        unlink('./videos/'.$delete.'.mp4');
        unlink('./videos/'.$delete.'.png');
        header("Location: ./studio");
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Admin Panel | DataTube</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://files.datalok.de/Logos/datatube.webp" type="image/x-icon">
    <link rel="stylesheet" href="./lib/style.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>
<body data-bs-theme="dark" <?php if ($edit!=null) {echo 'onLoad="loadModule()"';} ?>>
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
        <div id="content" class=" w-100 m-0">
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
                        <input name="s" class="form-control" style="padding-left: 35px;" type="search" value="<?php echo $suche?>">
                        <i class="fa fa-magnifying-glass searchicon"></i>
                    </div>
                </form>
                <div class="position-absolute top-0 pt-2 ps-1">
                    <a class="btn" data-bs-toggle="offcanvas" href="#sidebar" role="button" aria-controls="sidebar"><i class="fa-solid fa-bars"></i></a>
                    <a href="./"><img src="https://files.datalok.de/Logos/datatube.webp" width="32"></a>
                </div>
            </div>
            <div class="p-2 container">
                <table id="admintable" class="table">
                    <thead>
                        <tr>
                            <th style="width:110px;"><i class="fa-solid fa-image"></i></th>
                            <th>Titel</th>
                            <th>Datum</th>
                            <th><i class="fa-regular fa-thumbs-up"></i></th>
                            <th><i class="fa-solid fa-eye"></i></th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = mysqli_query($link, "SELECT * FROM youtube");
                    if (mysqli_num_rows($result)>0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $unixtime=$row["date"];
                            $datef=date('j. M Y H:i', $unixtime);
                            $articles='<tr>
                            <th><img src="./videos/'.$row["id"].'.png" width="100" style="aspect-ratio: 16/9;"></th>
                            <th>'.$row["title"].'</th>
                            <th>'.$datef.'</th>
                            <th>'.$row["likes"].'</th>
                            <th>'.$row["views"].'</th>
                            <th>
                                <a href="?edit='.$row["id"].'" title="Editieren" class="text-info fs-4 px-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="?del='.$row["id"].'" title="Löschen" class="text-danger fs-4 px-2"><i class="fa-solid fa-trash"></i></a>
                            </th>
                            </tr>';
                            echo $articles;
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php echo $msg?>
        </div>
    </div>
    <?php
    if ($edit!=null) {
        $result = mysqli_query($link, "SELECT title,description FROM youtube WHERE id='$edit'");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $title_modal=$row["title"];
                $title_desc=$row["description"];
            }
        } else {$modal_msg_notfound="<h2 class='text-danger'>Video nicht gefunden!</h2>";}
    }
    ?>
    <div id="editmodal" class="modal modal-lg" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Bearbeite Video <?php echo $edit?></h5></div>
                <form id="editform" action="" method="POST" class="modal-body">
                    <?php echo $modal_msg_notfound?>
                    <label class="form-label">Titel des Videos:</label>
                    <input class="form-control" type="text" name="title" value="<?php echo $title_modal?>" required><br>
                    <label class="form-label">Beschreibung:</label>
                    <textarea class="form-control" type="text" name="description" style="height:150px"><?php echo $title_desc?></textarea>
                </form>
                <div class="modal-footer">
                    <a href="./studio" class="btn btn-danger">Abbrechen</a>
                    <button type="submit" form="editform" class="btn btn-success">Speichern</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function () {
        $('#admintable').DataTable({
            scrollX: false,
            paging: false,
            info: false,
            order: [[ 2, 'desc' ]],
            columnDefs: [
                {orderable: false, targets: 0},
                {orderable: false, targets: 5}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json',
            },
        });
    });
</script>
<script>
function loadModule() {
    const editModal = new bootstrap.Modal('#editmodal');
    editModal.show();  
}
</script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://kit.fontawesome.com/09de98c34f.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</html>
<?php $link->close();?>