<?php
declare(strict_types=1);

namespace superjob\devino\message;

use InvalidArgumentException;

class StatusRequest implements IRequest
{
    private const MAX_MESSAGE_IDS_COUNT = 100;

    /**
     * @var string[]
     */
    protected $messageIds;

    /**
     * StatusRequest constructor.
     *
     * @param string[] $messageIds
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $messageIds)
    {
        if (count($messageIds) > self::MAX_MESSAGE_IDS_COUNT) {
            throw new InvalidArgumentException(
                sprintf('You cannot specify more than %d message ids', self::MAX_MESSAGE_IDS_COUNT)
            );
        }
        $this->messageIds = $messageIds;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return ['messages' => $this->messageIds];
    }
}