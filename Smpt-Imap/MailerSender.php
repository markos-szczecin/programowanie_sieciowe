<?php
/**
 * Created by PhpStorm.
 * User: Marek
 * Date: 03.02.2018
 * Time: 11:51
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerSender
{
    private $to = '';
    private $replyTo = '';
    /** @var string[] */
    private $attachmentPaths = array();
    private static $authConf = array();
    /** @var null|PHPMailer  */
    private $mailer = null;

    /**
     * @return array
     */
    private static function getAuthConf()
    {
        if (empty(self::$authConf)) {
            self::$authConf = json_decode(file_get_contents('auth.json'), true);
        }

        return self::$authConf;
    }

    private function attachFiles()
    {
        foreach ($this->attachmentPaths as $path) {
            $this->mailer->addAttachment($path);
        }
    }

    /**
     * @param string $to
     *
     * @return MailerSender
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return MailerSender
     */
    public function setReplyTo($to)
    {
        $this->replyTo = $to;

        return $this;
    }

    /**
     * MailerSender constructor.
     * @param string $subject
     * @param string $body
     * @param string $altBody
     * @throws Exception
     */
    public function __construct($subject = '', $body = '', $altBody = '')
    {
        $conf = self::getAuthConf();

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $conf['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $conf['username'];
        $mail->Password = $conf['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody;
        $mail->setFrom($conf['username']);

        $this->mailer = $mail;
    }

    /**
     * @param array $paths
     *
     * @return MailerSender
     */
    public function addAttachments(array $paths = array())
    {
        $this->attachmentPaths = $paths;

        return $this;
    }



    public function send()
    {
        $this->mailer->isHTML(true);
        $this->mailer->addAddress($this->to);
        $this->mailer->addReplyTo($this->replyTo);
        if ($this->attachmentPaths) {
            foreach ($this->attachmentPaths as $path) {
                $this->mailer->addAttachment($path, $path, 'base64');
            }
        }
        $this->mailer->send();
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public static function validateParams(array &$params)
    {
        $errors = array();
        $params['to'] = filter_var($params['to'], FILTER_SANITIZE_EMAIL);
        $params['reply'] = filter_var($params['reply'], FILTER_SANITIZE_EMAIL);
        if (empty($params['to']) || !filter_var($params['to'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Błędny adres email";
        }
        if (empty($params['reply']) || !filter_var($params['reply'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Błędny adres email 'reply to'";
        }
        $params['subject'] = filter_var($params['subject'], FILTER_SANITIZE_STRING);
        $params['body'] = filter_var($params['body'], FILTER_SANITIZE_STRING);
        $params['subject'] = filter_var($params['subject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $params['body'] = filter_var($params['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        return $errors;
    }
}