<?php
declare(strict_types=1);

namespace superjob\devino\message;

class SmsStatesResponse extends BaseResponse
{
    /**
     * @var SmsState[]
     */
    protected $smsStates;

    /**
     * StatusResponse constructor.
     *
     * @param string $code
     * @param string $providerId
     * @param array  $smsStates
     */
    public function __construct(string $code, string $providerId, array $smsStates)
    {
        parent::__construct($code, $providerId);
        $this->smsStates = $smsStates;
    }

    /**
     * @return SmsState[]
     */
    public function getSmsStates(): array
    {
        return $this->smsStates;
    }
}