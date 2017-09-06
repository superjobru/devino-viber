<?php
declare(strict_types=1);

namespace superjob\devino\content;

class ImageContent implements IMessageContent
{
    private const TYPE = 'image';

    /**
     * @var string
     */
    protected $imageUrl;

    /**
     * @param string $imageUrl
     */
    public function __construct(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'imageUrl' => $this->imageUrl,
        ];
    }
}