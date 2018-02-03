<?php
require 'session_conf.php';
require 'vendor/autoload.php';
use PhpImap\Mailbox;

require_once 'MailerReceiver.php';

try {
    $m = new MailerReceiver($_POST['host'], $_POST['port'], $_POST['security']);
    $m
        ->setUser($_POST['login'])
        ->setPassword($_POST['password']);
    if (isset($_POST['mail_id'])) {
        $s = $m->getSubjects();
        $_SESSION['mail_subject'] = $s[$_POST['mail_id']];
        $mail = $m->getMail($_POST['mail_id']);
        if (isset($mail->textHtml)) {
            $_SESSION['mail_data'] = $mail->textHtml;
        } else {
            $_SESSION['mail_data'] = $mail->textPlain;
        }
        $_SESSION['attachments'] = $m->getAttachments();
    } else {
        $a = $m->getSubjects();
    }
    header('Location: index.php?key=' . $m->getSessionKey());
    exit;
} catch (Exception $e) {
    header('Location: index.php?error=1');
}
$mailbox = new Mailbox('{mail.zut.edu.pl:143/imap/tls}INBOX', 'pm26923', 'GREbocinianin@Zs1209', __DIR__);

// Read all messaged into an array:
$mailsIds = $mailbox->searchMailbox('ALL');
if(!$mailsIds) {
    die('Mailbox is empty');
}

// Get the first message and save its attachment(s) to disk:
$mail = $mailbox->getMail($mailsIds[0]);

print_r($mail);
echo "\n\nAttachments:\n";
print_r($mail->getAttachments());