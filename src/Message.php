<?php
declare(strict_types=1);

namespace superjob\devino;

use JsonSerializable;
use superjob\devino\content\IMessageContent;
use superjob\devino\content\SmsContent;

class Message implements JsonSerializable
{
    private const TYPE = 'viber';
    /**
     * @var string
     */
    protected $subject;
    /**
     * @var string
     */
    protected $priority = 'high';
    /**
     * @var int
     */
    protected $validityPeriodSec = 3600;
    /**
     * @var string
     */
    protected $comment;
    /**
     * @var IMessageContent
     */
    protected $content;
    /**
     * @var string
     */
    protected $address;
    /**
     * @var SmsContent
     */
    protected $sms;

    /**
     * Message constructor.
     *
     * @param string          $address
     * @param string          $subject
     * @param string          $comment
     * @param IMessageContent $content
     * @param null|SmsContent $sms
     */
    public function __construct(string $address, string $subject, string $comment, IMessageContent $content, ?SmsContent $sms = null)
    {
        $this->subject = $subject;
        $this->comment = $comment;
        $this->content = $content;
        $this->address = $address;
        $this->sms = $sms;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $content = [
            'subject' => $this->subject,
            'priority' => $this->priority,
            'validityPeriodSec' => $this->validityPeriodSec,
            'comment' => $this->comment,
            'type' => self::TYPE,
            'contentType' => $this->content->getType(),
            'content' => $this->content,
            'address' => $this->address,
        ];

        if (null !== $this->sms) {
            $content['sms'] = $this->sms;
        }

        return $content;
    }

    /**
     * @param string $priority
     *
     * @return $this
     */
    public function setPriority(string $priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @param int $validityPeriodSec
     *
     * @return $this
     */
    public function setValidityPeriodSec(int $validityPeriodSec)
    {
        $this->validityPeriodSec = $validityPeriodSec;
        return $this;
    }
}