<?php

declare(strict_types=1);

namespace Strata\Frontend\ResponseHelper;

use Psr\Http\Message\ResponseInterface;

/**
 * Concrete implementation of response helper using PSR7 response objects
 */
class Psr7ResponseHelper extends ResponseHelperAbstract
{
    /**
     * Apply headers to response object and return response
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function apply(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->getHeaders() as $name => $values) {
            /** @var HeaderValue $header */
            foreach ($values as $header) {
                if ($header->isReplace()) {
                    $response = $response->withHeader($name, $header->getValue());
                } else {
                    $response = $response->withAddedHeader($name, $header->getValue());
                }
            }
        }
        return $response;
    }
}
