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
    private $type;

    /** @var string */
    private $message;

    public function __construct(string $message = '', string $type = self::TYPE_INFO)
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): FLashMessage
    {
        $this->type = $type;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): FLashMessage
    {
        $this->message = $message;

        return $this;
    }
}
