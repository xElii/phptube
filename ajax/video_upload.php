<?php
require_once("../lib/config.php");
// Create Unique ID for Video
function generateRandomID($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomID = '';
    for ($i = 0; $i < $length; $i++) {
        $randomID .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomID;
}
$uniqueID = '';
$isUnique = false;

while (!$isUnique) {
    $uniqueID = generateRandomID();
    $query = "SELECT id FROM youtube WHERE id='$uniqueID'";
    $result = $link->query($query);
    if ($result->num_rows == 0) {$isUnique = true;}
}

$targetDir = '../videos/';
// Set Variables for thumbnail
$thumbnail=$_FILES['thumbnail'];
$thumbnailname=$_FILES['thumbnail']['name'];
$thumbnailext=pathinfo($thumbnailname, PATHINFO_EXTENSION);
$thumbnailtarget=$targetDir.$uniqueID.'.'.$thumbnailext;

// Upload Video
$file=$_FILES['file'];
$filename=$_FILES['file']['name'];
$fileExtension=pathinfo($filename, PATHINFO_EXTENSION);
$targetFile=$targetDir.$uniqueID.'.'.$fileExtension;

$uploadOk = true;
$uploadSpeed = 0;

if ($fileExtension!="mp4" || $thumbnailext!="png") {$uploadOk=false;echo('<p class="alert alert-danger">Das Video sollte mp4 sein und das Thumbnail png!</p>');}

if ($uploadOk) {
    if (move_uploaded_file($file['tmp_name'], $targetFile) && move_uploaded_file($thumbnail['tmp_name'], $thumbnailtarget)) {
        $title=$_POST['title'];
        $description=$_POST['description'];
        $creator=$_POST['creator'];
        $unixtime=time();
        $sql = "INSERT INTO youtube (id, title, description, creator, date) VALUES ('$uniqueID', '$title', '$description', '$creator', '$unixtime')";
        if ($link->query($sql) === true) {echo ('<p class="alert alert-success">Video wurde hochgeladen!</p>');}
    } else {echo '<p class="alert alert-danger">Fehler beim Hochladen!</p>';}
}
$link->close();
?>
