<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Studio24\Frontend\Api\ApiPermissionHelper;
use Studio24\Frontend\Exception\NotFoundException;
use Studio24\Frontend\Exception\PermissionException;
use Studio24\Frontend\Exception\FailedRequestException;
use Studio24\Frontend\Exception\ApiException;
use Studio24\Frontend\Traits\LoggerTrait;
use Studio24\Frontend\Version;

/**
 * Simple abstract class for communicating with a RESTful API
 *
 * @package Studio24\Frontend
 */
abstract class AbstractApiProvider
{
    use LoggerTrait;

    /**
     * Keep track of total number of requests per page load
     *
     * @var int
     */
    protected $totalRequests = 0;

    /**
     * What is the limit for paginated results
     *
     * @var int
     */
    public $paginationLimit = 10;

    /**
     * API base URI
     *
     * @var string
     */
    protected $baseUri;

    /**
     * HTTP client to access the API
     *
     * @var Client
     */
    protected $client;

    /**
     * ApiPermissionHelper for accessing the API
     *
     * Used to protect against accidental misuse
     *
     * @var ApiPermissionHelper
     */
    protected $permissions;

    /**
     * Expected response code from requests, if these do not match throw an exception
     *
     * @var bool
     */
    protected $expectedResponseCode = 200;

    /**
     * Array of response error codes to ignore and not throw an exception for
     *
     * @var array
     */
    protected $ignoreErrorCodes = [401];

    /**
     * Default values for response error codes to ignore
     *
     * @var array
     */
    protected $defaultIgnoredErrorCodes = [401];

    /**
     * Constructor
     *
     * @param string $baseUri API base URI
     * @param ApiPermissionHelper $permissions (if not passed, default = read-only)
     */
    public function __construct(string $baseUri, ApiPermissionHelper $permissions = null)
    {
        $this->setBaseUri($baseUri);

        if ($permissions instanceof ApiPermissionHelper) {
            $this->permissions = $permissions;
        } else {
            $this->permissions = new ApiPermissionHelper(ApiPermissionHelper::READ);
        }
    }

    /**
     * Set the API base URI
     *
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * Return API base URI
     *
     * @return string
     * @throws ApiException
     */
    public function getBaseUri()
    {
        if (empty($this->baseUri)) {
            throw new ApiException(sprintf('API base URL not set, please set via %s::setBaseUri()', get_class($this)));
        }

        return $this->baseUri;
    }

    /**
     * Setup and return the HTTP client to communicate with the API
     *
     * @return Client
     * @throws \Studio24\Frontend\Exception\ApiException
     */
    public function setupHttpClient() : Client
    {
        return new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => [
                'User-Agent' => $this->getUserAgent(),
            ]
        ]);
    }

    /**
     * Return the user agent string to use with HTTP requests
     *
     * @return string
     */
    public function getUserAgent()
    {
        return Version::getUserAgent();
    }

    /**
     * Set expected response code for subsequent requests, if these do not match throw an exception
     *
     * @param integer $code
     * @throws \Exception
     */
    public function expectedResponseCode(int $code)
    {
        $this->expectedResponseCode = $code;
    }

    /**
     * Clear out any expected response code for subsequent requests
     */
    public function clearExpectedResponseCode()
    {
        $this->expectedResponseCode = null;
    }

    /**
     * Set an error code to be ignored in the response (and not throw an exception)
     *
     * @param int $code
     */
    public function ignoreErrorCode(int $code)
    {
        $this->ignoreErrorCodes[] = $code;
    }

    /**
     * Restore default error codes to be ignored in the response (and not throw an exception)
     *
     * Example use case: when querying a page that exists but has a featured media that does not exist (returns a 404),
     * it's best to ignore the 404 error returned by the media query but revert to catching 404 errors for subsequent queries.
     *
     */
    public function restoreDefaultIgnoredErrorCodes()
    {
        $this->ignoreErrorCodes = $this->defaultIgnoredErrorCodes;
    }

    /**
     * Check whether you are allowed to perform the following action on the API
     *
     * @param int $action
     * @throws PermissionException
     */
    public function checkPermission(int $action)
    {
        if (!$this->permissions->isAllowed($action)) {
            $message = sprintf('Permission not allowed error. Requested permission: %s, Allowed permissions: %s', $this->permissions->getName($action), $this->permissions->__toString());
            throw new PermissionException($message);
        }
    }

    /**
     * Check whether you are allowed to perform a READ operation on the API
     *
     * @throws PermissionException
     */
    public function permissionRead()
    {
        $this->checkPermission(ApiPermissionHelper::READ);
    }

    /**
     * Check whether you are allowed to perform a WRITE operation on the API
     *
     * @throws PermissionException
     */
    public function permissionWrite()
    {
        $this->checkPermission(ApiPermissionHelper::WRITE);
    }

    /**
     * Check whether you are allowed to perform a DELETE operation on the API
     *
     * @throws PermissionException
     */
    public function permissionDelete()
    {
        $this->checkPermission(ApiPermissionHelper::DELETE);
    }

    /**
     * Set HTTP client
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Return the HTTP client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }

        $this->setClient($this->setupHttpClient());

        return $this->client;
    }

    /**
     * Make a request to the API
     *
     * @param $method
     * @param $uri
     * @param array $options
     * @return ResponseInterface
     * @throws FailedRequestException
     * @throws NotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, array $options): ResponseInterface
    {
        // Suppress HTTP errors raising Guzzle exceptions
        $options = array_merge($options, ['http_errors' => false]);

        if ($this->hasLogger()) {
            $this->getLogger()->info(sprintf('REST API request: %s %s (options: %s)', $method, $uri, $this->formatArray($options)));
        }

        $response = $this->getClient()->request($method, $uri, $options);
        $this->totalRequests++;

        if ($response->getStatusCode() == $this->expectedResponseCode) {
            return $response;
        }

        // Return empty response for expected errors
        if (in_array($response->getStatusCode(), $this->ignoreErrorCodes)) {
            return new Response($response->getStatusCode(), [], '[]', '1.1', $response->getReasonPhrase());
        } else {
            $message = sprintf('Failed HTTP response. Expected: %s, Actual: %s, Error: %s', $this->expectedResponseCode, $response->getStatusCode(), $response->getReasonPhrase());

            if (substr((string) $response->getStatusCode(), 0, 1) === '4') {
                throw new NotFoundException($message, $response->getStatusCode());
            } else {
                throw new FailedRequestException($message, $response->getStatusCode());
            }
        }
    }
    

    /**
     * Make a GET request to the API
     *
     * @param string $uri URI relative to base URI
     * @param array $options
     * @return ResponseInterface
     * @throws FailedRequestException
     * @throws NotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($uri, array $options = []) : ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * Make a POST request to the API
     *
     * @param string $uri URI relative to base URI
     * @param array $options
     * @return ResponseInterface
     * @throws FailedRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($uri, array $options) : ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * Make a HEAD request to the API
     *
     * @param string $uri URI relative to base URI
     * @param array $options
     * @return ResponseInterface
     * @throws FailedRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function head($uri, array $options) : ResponseInterface
    {
        return $this->request('HEAD', $uri, $options);
    }

    /**
     * Parse return JSON data and return in decoded format
     *
     * @param ResponseInterface $response
     * @return array Array of response data
     * @throws FailedRequestException
     */
    public function parseJsonResponse(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->__toString(), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        throw new FailedRequestException('Error parsing JSON response body: ' . json_last_error_msg());
    }

    /**
     * Return number of total requests made
     *
     * @return int
     */
    public function getTotalRequests(): int
    {
        return $this->totalRequests;
    }

    /**
     * Determine what the limit for the number of paginated results is
     *
     * @param array $options
     * @return int
     */
    public function resolvePaginationLimit(array $options): int
    {
        if (isset($options['limit'])) {
            return $options['limit'];
        }

        if (isset($options['per_page'])) {
            return $options['per_page'];
        }

        return $this->paginationLimit;
    }
}
