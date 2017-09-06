<?php
declare(strict_types=1);

namespace superjob\devino\message;

class SmsState
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $state;

    /**
     * SmsState constructor.
     *
     * @param string $id
     * @param string $state
     */
    public function __construct($id, $state)
    {
        $this->id = (string) $id;
        $this->state = (string) $state;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }
}