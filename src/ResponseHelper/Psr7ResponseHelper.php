<?php

declare(strict_types=1);

namespace Strata\Frontend\ResponseHelper;

use Psr\Http\Message\ResponseInterface;

/**
 * Concrete implementation of response helper using PSR7 response objects
 */
class Psr7ResponseHelper extends ResponseHelperAbstract
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->setResponse($response);
    }

    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set a header to the response object
     * @param string $name
     * @param string $value
     * @param bool $replace If true, replace header, if false, append header
     * @return $this
     */
    public function setHeader(string $name, string $value, bool $replace = true): self
    {
        if ($replace) {
            $this->setResponse($this->response->withHeader($name, $value));
        } else {
            $this->setResponse($this->response->withAddedHeader($name, $value));
        }

        return $this;
    }

}
