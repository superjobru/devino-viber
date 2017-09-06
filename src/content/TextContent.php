<?php
declare(strict_types=1);

namespace superjob\devino\content;

class TextContent implements IMessageContent
{
    private const TYPE = 'text';
    /**
     * @var string
     */
    protected $text;

    /**
     * TextContent constructor.
     *
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}