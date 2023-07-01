<?php
require_once("../lib/config.php");

$id=$_GET["id"] ?? null;

// Get Current Likes
$result = mysqli_query($link, "SELECT likes FROM youtube WHERE id='$id'");
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $likes=$row["likes"];
        }
    } else {$link->close();exit('Video <b>'.$id.'</b> nicht vorhanden!');}

// Add one like
$sql = "UPDATE youtube SET likes=$likes+1 WHERE id='$id'";
if ($link->query($sql) === TRUE) {echo('Like zu '.$id.' hinzugefügt!');}

$link->close();
?>