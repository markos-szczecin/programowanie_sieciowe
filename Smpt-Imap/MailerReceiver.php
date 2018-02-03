<?php
use PhpImap\Mailbox;

class MailerReceiver
{
    private $host;
    private $port;
    private $security;
    private $user;
    private $password;
    /** @var IncomingMailAttachment */
    private $attachments;
    private $mailbox;

    /**
     * @return string
     */
    public function getSessionKey()
    {
        return $this->host . $this->user;
    }

    /**
     * @return string
     */
    private function getImapPath()
    {
        return '{' . $this->host . ':' . $this->port . '/imap/' . mb_strtolower($this->security) . '}INBOX';
    }

    public function __construct($host, $port, $security)
    {
        $this->host = $host;
        $this->port = $port;
        $this->security = $security;
    }

    /**
     * @param strin $user
     * @return MailerReceiver
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $pass
     * @return MailerReceiver
     */
    public function setPassword($pass)
    {
        $this->password = $pass;

        return $this;
    }

    /**
     * @return Mailbox
     * @throws \PhpImap\Exception
     */
    private function getMailBox()
    {
        if (!$this->mailbox) {
            $this->mailbox = new Mailbox($this->getImapPath(), $this->user, $this->password, __DIR__ . '/attachments');
        }

        return $this->mailbox;
    }

    /**
     * @return array
     * @throws \PhpImap\Exception
     */
    public function fetch()
    {
        return $this->getMailBox()->searchMailbox('UNSEEN');
    }

    /**
     * @param $id
     * @return \PhpImap\IncomingMail
     * @throws \PhpImap\Exception
     */
    public function getMail($id)
    {
        $ret = $this->getMailBox()->getMail($id);
        $this->attachments = $ret->getAttachments();

        return $ret;
    }

    public function getAttachments()
    {
        $attachments = array();
        if ($this->attachments) {
            /** @var IncomingMailAttachment $a */
            foreach ($this->attachments as $a) {
                $attachments[] = $a->filePath;
            }
        }
        return $attachments;
    }

    /**
     * @return array
     * @throws \PhpImap\Exception
     */
    public function getSubjects()
    {
        $subjects = array();
        $ids = $this->fetch();
        $oldIds = $this->getIds();
        if (sha1(json_encode($ids)) != sha1(json_encode($oldIds))) {
            $this->saveIds($ids);
        }
        $oldIds = array_flip(($oldIds));
        foreach ($ids as $id) {
            if (!isset($oldIds[$id])) {
                $subjects[$id] = $this->getMail($id)->subject;
            }
        }
        $this->saveSubjects($subjects);
        return json_decode($_SESSION[$this->getSessionKey()], true);
    }

    private function saveSubjects($subjects)
    {
        if (empty($_SESSION[$this->getSessionKey()])) {
            $_SESSION[$this->getSessionKey()] = json_encode($subjects);
        } else {
            $oldSubs = $_SESSION[$this->getSessionKey()];
            if (sha1($oldSubs) !== json_encode($subjects)) {
                $oldSubs = json_decode($oldSubs, true);
                $subjects = $oldSubs + $subjects;
                $_SESSION[$this->getSessionKey()] = json_encode($subjects);
            }
        }
    }

    /**
     * @param array $ids
     */
    private function saveIds($ids)
    {
        if (empty($_SESSION[$this->getSessionKey() . 'ids']) || sha1($_SESSION[$this->getSessionKey() . 'ids']) !== json_encode($ids)) {
            $_SESSION[$this->getSessionKey() . 'ids'] = json_encode($ids);

        }
    }

    /**
     * @return array
     */
    private function getIds()
    {
        if (empty($_SESSION[$this->getSessionKey() . 'ids'])) {
            return array();
        } else {
            $data = $_SESSION[$this->getSessionKey() . 'ids'];
            if ($data) {
                return json_decode($data, true);
            }
            return array();
        }
    }


}