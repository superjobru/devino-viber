<?php
declare(strict_types=1);

namespace superjob\devino\message;

use InvalidArgumentException;
use superjob\devino\Message;

class SendRequest implements IRequest
{
    /**
     * @var Message[]
     */
    protected $messages;

    /**
     * SendRequest constructor.
     *
     * @param Message[] $messages
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $messages)
    {
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
        $resendSms = false;
        foreach ($this->messages as $message) {
            if ($message->hasSms()) {
                $resendSms = true;
            }
        }

        return [
            'resendSms' => $resendSms,
            'messages' => $this->messages,
        ];
    }
}