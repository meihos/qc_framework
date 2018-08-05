<?php
namespace Core\Structure\Entities;

/**
 * Class Mail
 * @package Core\Structure\Entities
 */
class Mail
{

    protected $from;
    protected $replyTo;
    protected $headers;

    protected $to;
    protected $bcc;
    protected $cc;

    protected $subject;
    protected $body;
    protected $altBody;
    protected $isHtml;
    protected $charset;
    protected $attachment;

    public function __construct()
    {
        $this->from = ['email' => null, 'name' => null];
        $this->replyTo = [];
        $this->headers = [];

        $this->subject = '';
        $this->body = '';
        $this->altBody = '';
        $this->charset = '';

        $this->bcc = [];
        $this->cc = [];
        $this->to = [];
        $this->attachment = [];
        $this->isHtml = false;
    }

    /**
     * @return array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param $email
     * @param null $name
     * @return $this
     */
    public function setFrom($email, $name = null)
    {
        if ($this->checkEmail($email)) {
            $this->from['email'] = $email;
            $this->from['name'] = $name;
        }

        return $this;
    }

    private function checkEmail($email)
    {
        if (!empty($email) && (filter_var($email, FILTER_VALIDATE_EMAIL))) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param $email
     * @param null $name
     * @return $this
     */
    public function addReplyTo($email, $name = null)
    {
        if ($this->checkEmail($email)) {
            $this->replyTo[] = [
                'email' => $email,
                'name' => $name,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addHeader($name, $value)
    {
        if (!empty($name) && !empty($value)) {
            $this->headers[] = [
                'name' => $name,
                'value' => $value,
            ];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getAltBody()
    {
        return $this->altBody;
    }

    /**
     * @param $altBody
     * @return $this
     */
    public function setAltBody($altBody)
    {
        $this->altBody = $altBody;
        return $this;
    }

    /**
     * @param $flag
     * @return $this
     */
    public function setHTML($flag)
    {
        $this->isHtml = (bool)$flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHTML()
    {
        return $this->isHtml;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param $email
     * @param null $name
     * @return $this
     */
    public function addTo($email, $name = null)
    {
        if ($this->checkEmail($email)) {
            $this->to[] = [
                'email' => $email,
                'name' => $name,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCC()
    {
        return $this->cc;
    }

    /**
     * @param $email
     * @param null $name
     * @return $this
     */
    public function addCC($email, $name = null)
    {
        if ($this->checkEmail($email)) {
            $this->cc[] = [
                'email' => $email,
                'name' => $name,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getBCC()
    {
        return $this->bcc;
    }

    /**
     * @param $email
     * @param null $name
     * @return $this
     */
    public function addBCC($email, $name = null)
    {
        if ($this->checkEmail($email)) {
            $this->bcc[] = [
                'email' => $email,
                'name' => $name,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachment;
    }

    /**
     * @param $path
     * @param $name
     * @return $this
     */
    public function addAttachments($path, $name)
    {
        $this->attachment[] = [
            'path' => $path,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * @return array
     */
    public function createArrayData()
    {
        return [
            'from' => $this->from,
            'replyTo' => $this->replyTo,
            'headers' => $this->headers,
            'to' => $this->to,
            'bcc' => $this->bcc,
            'cc' => $this->cc,
            'subject' => $this->subject,
            'body' => $this->body,
            'altBody' => $this->altBody,
            'charset' => $this->charset,
            'isHtml' => $this->isHtml,
            'attachment' => $this->attachment,
        ];
    }
}