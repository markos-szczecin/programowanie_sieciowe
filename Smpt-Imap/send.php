<?php
require 'vendor/autoload.php';
require_once 'MailerSender.php';
try {
    $errors = MailerSender::validateParams($_POST);
    if ($errors) {
        echo implode('<br />', $errors);

        exit;
    }
    $mailer = new MailerSender($_POST['subject'], $_POST['body'], "");
    $mailer
        ->setReplyTo($_POST['reply'])
        ->setTo($_POST['to']);
    if (isset($_FILES['attachment'])) {
        move_uploaded_file($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
        $mailer->addAttachments(array($_FILES['attachment']['name']));
    }
    $mailer->send();
    if (isset($_FILES['attachment'])) {
        unlink($_FILES['attachment']['name']);
    }
    header('Location: index.php?success=1');
} catch (Exception $e) {
    var_dump($e->getMessage());
    header('Location: index.php?success=0');
}