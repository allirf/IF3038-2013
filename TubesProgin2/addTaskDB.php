<?php

require_once('config.php');
?>

<?php

session_start();

if (connectDB()) {
    $taskname = $_POST['namaTask'];
    $deadline = $_POST['newDeadlineTask'];
    $assignee = $_POST['newAssigneeTask'];
    $tag = $_POST['newTagTask'];
    $file = $_FILES['attachfile']['tmp_name'];
    $category = 1;

    $insertTaskQuery = "INSERT INTO `task` (`IDTask` ,`IDCategory` ,`TaskName` ,`Status` ,`Deadline`)
        VALUES (NULL , '" . $category . "', '" . $taskname . "', 'undone', '" . $deadline . "');";
    $insertTask = mysql_query($insertTaskQuery);

    $idTaskQuery = "SELECT * from task";
    $idTaskList = mysql_query($idTaskQuery);
    $idTask = mysql_num_rows($idTaskList);

    $assigneelist = explode(";", $assignee);

    $num_assignee = count($assigneelist);
    for ($x = 0; $x < $num_assignee-1; $x++) {
        $addAssigneeQuery = "INSERT INTO assignment (`IDAssignment`, `Username`, `IDTask`) 
            VALUES (NULL, '" . $assigneelist[$x] . "', '" . $idTask . "');";
        $addAssignee = mysql_query($addAssigneeQuery);  
    }

    $taglist = explode(",", $tag);
    $num_tag = count($taglist);
    for ($x = 0; $x < $num_tag; $x++) {
        $CheckTagQuery = "SELECT * FROM tag where TagName='" . $taglist[$x] . "';";
        $CheckTag = mysql_query($CheckTagQuery);
        if (mysql_num_rows($CheckTag) > 0) {
            $FetchTag = mysql_fetch_array($CheckTag);
            $idTag = $FetchTag[0];
        } else {
            $insertTagQuery = "INSERT INTO `tag` (`IDTag` ,`TagName`)
                VALUES (NULL , '" . $taglist[$x] . "');";
            $insertTag = mysql_query($insertTagQuery);
            $idTagQuery = "SELECT * from tag";
            $idTagList = mysql_query($idTagQuery);
            $idTag = mysql_num_rows($idTagList);
        }
        $insertTaskTagQuery = "INSERT INTO `tasktag` (`IDTaskTag` ,`IDTask` ,`IDTag`)
                VALUES (NULL , '".$idTask."', '".$idTag."');";
        $insertTaskTag = mysql_query($insertTaskTagQuery);
    }


    if (isset($file)) {
        $num_files = count($file);
        for ($x = 0; $x < $num_files; $x++) {
            $filename = "attachment/" . $taskname . "_" . $_FILES["attachfile"]["name"][$x];
            move_uploaded_file($file[$x], $filename);
            $insertAttachmentQuery = "INSERT INTO `attachment` (`IDAttachment`, `IDTask`, `PathFile`) 
                VALUES (NULL, '".$idTask."', '".$filename."');";
            $insertAttachment = mysql_query($insertAttachmentQuery);
        }
    }
    header('Location: Dashboard.php');
}
?>