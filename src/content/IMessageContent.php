<?php
declare(strict_types=1);

namespace superjob\devino\content;

use JsonSerializable;

interface IMessageContent extends JsonSerializable
{
    /**
     * Returns message type
     *
     * @return string
     */
    public function getType(): string;
}