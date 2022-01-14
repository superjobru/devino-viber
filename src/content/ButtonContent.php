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
     * @var TextContent
     */
    protected $textContent;
    /**
     * @var ImageContent|null
     */
    protected $imageContent;

    /**
     * ImageTextContent constructor.
     *
     * @param string $caption
     * @param string $action
     * @param TextContent $textContent
     * @param ImageContent|null $imageContent
     */
    public function __construct(string $caption, string $action, TextContent $textContent, ?ImageContent $imageContent = null)
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
        $data = [
            'caption' => $this->caption,
            'action' => $this->action,
            'text' => $this->textContent->getText(),
        ];

        if (null !== $this->imageContent) {
            $data['imageUrl'] = $this->imageContent->getImageUrl();
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }
}