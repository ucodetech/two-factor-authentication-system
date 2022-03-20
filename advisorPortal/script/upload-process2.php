<?php
require_once '../../core/init.php';
$advisor = new Admin();
$fileupload = new FileUpload();
$show = new Show();
$error = array();
$advisor_id = $advisor->data()->id;
$db = Database::getInstance();

$file = Input::get('signature_file');

$filename = $file['name'];


if (empty($file['name'])) {
    echo $show->showMessage('danger', 'File cant be empty!', 'warning');
    return false;
}
if (!$fileupload->isImage($filename)) {
    echo $show->showMessage('danger', 'File is not a valid image!', 'warning');
    return false;

}
if ($fileupload->fileSize($filename)) {
    echo $show->showMessage('danger', 'File size is too large!', 'warning');
    return false;
}

$ds = DIRECTORY_SEPARATOR;
$temp_file = $file['tmp_name'];
$file_path = $fileupload->moveFile($temp_file, "indusSupervisor","signature", $filename)->path();
$db_path = $file_path;
$date = date('Y-m-d');
$Update = "UPDATE inds_supervisors SET signature = '$db_path' WHERE id = '$advisor_id' ";
if ($db->query($Update))
    echo 'success';
