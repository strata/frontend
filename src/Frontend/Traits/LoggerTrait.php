<?php

namespace Studio24\Frontend\Traits;

use Psr\Log\LoggerInterface;

/**
 * Supports logger dependency injection
 *
 * @package Studio24\Frontend\Traits
 */
trait LoggerTrait
{

    /**
     * Logger object
     *
     * @var LoggerInterface
     */
    public $logger;

    /**
     * Whether a valid logger is set
     *
     * @return bool
     */
    public function hasLogger(): bool
    {
        return ($this->logger instanceof LoggerInterface);
    }

    /**
     * Set the logger object
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger object
     *
     * @see https://symfony.com/doc/current/logging.html
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }
}
