<?php
declare(strict_types=1);

namespace superjob\devino\message;

use InvalidArgumentException;
use superjob\devino\Message;

class SendRequest implements IRequest
{
    /**
     * @var bool
     */
    protected $resendSms;
    /**
     * @var Message[]
     */
    protected $messages;

    /**
     * SendRequest constructor.
     *
     * @param bool      $resendSms
     * @param Message[] $messages
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $messages, bool $resendSms = false)
    {
        $this->resendSms = $resendSms;
        foreach ($messages as $message) {
            if (!$message instanceof Message) {
                throw new InvalidArgumentException('Only type Message and its subtypes are permitted');
            }
        }
        $this->messages = $messages;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'resendSms' => $this->resendSms,
            'messages' => $this->messages,
        ];
    }
}