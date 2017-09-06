<?php
declare(strict_types=1);

namespace superjob\devino\content;

use JsonSerializable;

class SmsContent implements JsonSerializable
{
    /**
     * @var string
     */
    protected $text;
    /**
     * @var string
     */
    protected $sender;
    /**
     * @var int
     */
    protected $validityPeriodSec = 5000;

    /**
     * SmsContent constructor.
     *
     * @param string $text
     * @param string $sender
     */
    public function __construct(string $text, string $sender)
    {
        $this->text = $text;
        $this->sender = $sender;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'smsText' => $this->text,
            'smsSrcAddress' => $this->sender,
            'smsValidityPeriodSec' => $this->validityPeriodSec,
        ];
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