<?php
declare(strict_types=1);

namespace superjob\devino\message;

class StatusResponse extends BaseResponse
{
    /**
     * @var string
     */
    protected $status;
    /**
     * @var string
     */
    protected $statusAt;

    /**
     * StatusResponse constructor.
     *
     * @param string $code
     * @param string $providerId
     * @param string $status
     * @param string $statusAt
     */
    public function __construct(string $code, string $providerId, string $status, string $statusAt)
    {
        parent::__construct($code, $providerId);
        $this->status = $status;
        $this->statusAt = $statusAt;
    }

    /**
     * @return string|null
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getStatusAt(): string
    {
        return $this->statusAt;
    }
}