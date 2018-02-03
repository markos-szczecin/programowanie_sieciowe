<?php
if (isset($_FILES['file_to_convert'])) {
    $t = '_encoded';
} else {
    $t = '_decoded';
}
error_reporting(0);


require_once 'Base64Converter.php';

$params = $_POST;
if (isset($_FILES['file_to_convert'])) {
    move_uploaded_file($_FILES['file_to_convert']['tmp_name'], $_FILES['file_to_convert']['name']);
}
$b = new Base64Converter();
try {
    if ($t === '_encoded') {
        file_put_contents('temp', $b->convertFile($_FILES['file_to_convert']['name']));
        $mime = 'text/plain';
    } else {
        file_put_contents('temp', $b->decode($_POST['text']));
        $mime =  mime_content_type('temp');
    }
} catch (Exception $e) {
    switch ($e->getCode()) {
        case Base64Converter::EMPTY_DATA_ERROR:
            echo "No sent data detected";
            break;
        case Base64Converter::INVALID_BASE64_ERROR:
            echo "Invalid base64 string";
            break;
    }
}
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="your_file' . date('Y-m-d-H-i') . $t .'"');
header('Expires: 0');
echo file_get_contents('temp');
exit;