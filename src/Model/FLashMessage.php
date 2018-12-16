<?php

namespace App\Model;

/**
 * Class FLashMessage
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class FLashMessage
{
    const TYPE_INFO = 'notice';
    const TYPE_ERROR = 'error';

    /** @var string */
    private $type = self::TYPE_INFO;

    /** @var string */
    private $message;

    /**
     * FLashMessage constructor.
     *
     * @param string $message
     * @param string $type
     */
    public function __construct(string $message = '', string $type = self::TYPE_INFO)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return FLashMessage
     */
    public function setType(string $type): FLashMessage
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return FLashMessage
     */
    public function setMessage(string $message): FLashMessage
    {
        $this->message = $message;

        return $this;
    }
}
