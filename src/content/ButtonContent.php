<?php
declare(strict_types=1);

namespace superjob\devino\content;

class ButtonContent implements IMessageContent
{
    private const TYPE = 'button';
    /**
     * @var string
     */
    protected $caption;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $imageUrl;
    /**
     * @var TextContent
     */
    protected $textContent;
    /**
     * @var ImageContent
     */
    protected $imageContent;

    /**
     * ImageTextContent constructor.
     *
     * @param string       $caption
     * @param string       $action
     * @param TextContent  $textContent
     * @param ImageContent $imageContent
     */
    public function __construct(string $caption, string $action, TextContent $textContent, ImageContent $imageContent)
    {
        $this->caption = $caption;
        $this->action = $action;
        $this->textContent = $textContent;
        $this->imageContent = $imageContent;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'caption' => $this->caption,
            'action' => $this->action,
            'text' => $this->textContent->getText(),
            'imageUrl' => $this->imageContent->getImageUrl(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }
}