<?php
declare(strict_types=1);

namespace superjob\devino\message;

abstract class BaseResponse implements IResponse
{
    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $providerId;

    /**
     * SendResponse constructor.
     *
     * @param string $code
     * @param string $providerId
     */
    public function __construct(string $code, string $providerId)
    {
        $this->code = $code;
        $this->providerId = $providerId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getProviderId(): string
    {
        return $this->providerId;
    }
}