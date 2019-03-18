<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Studio24\Frontend\Exception\PermissionException;
use Studio24\Frontend\Exception\FailedRequestException;
use Studio24\Frontend\Exception\ApiException;
use Studio24\Frontend\Version;

/**
 * Simple abstract class for communicating with a RESTful API
 *
 * @package Studio24\Frontend
 */
abstract class RestApiAbstract
{
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
     * Permissions for accessing the API
     *
     * Used to protect against accidental misuse
     *
     * @var Permissions
     */
    protected $permissions;

    /**
     * Expected response code from requests, if these do not match throw an exception
     *
     * @var bool
     */
    protected $expectedResponseCode;

    /**
     * Array of response error codes to ignore and not throw an exception for
     *
     * @var array
     */
    protected $ignoreErrorCodes = [401];

    /**
     * Constructor
     *
     * @param string $baseUri API base URI
     * @param Permissions $permissions (if not passed, default = read-only)
     */
    public function __construct(string $baseUri, Permissions $permissions = null)
    {
        $this->setBaseUri($baseUri);

        if ($permissions instanceof Permissions) {
            $this->permissions = $permissions;
        } else {
            $this->permissions = new Permissions(Permissions::READ);
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
     */
    abstract public function setupHttpClient() : Client;

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
        $this->checkPermission(Permissions::READ);
    }

    /**
     * Check whether you are allowed to perform a WRITE operation on the API
     *
     * @throws PermissionException
     */
    public function permissionWrite()
    {
        $this->checkPermission(Permissions::WRITE);
    }

    /**
     * Check whether you are allowed to perform a DELETE operation on the API
     *
     * @throws PermissionException
     */
    public function permissionDelete()
    {
        $this->checkPermission(Permissions::DELETE);
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
     * @param $method (GET, POST)
     * @param $uri  URI relative to base URI
     * @param array $options
     * @return ResponseInterface
     * @throws FailedRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, array $options) : ResponseInterface
    {
        try {
            $response = $this->getClient()->request($method, $uri, $options);

        } catch (RequestException $e) {
            if (in_array($e->getCode(), $this->ignoreErrorCodes)) {
                // @todo Log warning?

                // Return empty error response to frontend
                return new Response($e->getCode(), [], '[]', '1.1', $e->getMessage());
            }
        }

        if ($this->expectedResponseCode !== null) {
            if ($response->getStatusCode() !== $this->expectedResponseCode) {
                $message = sprintf('Expected HTTP response code error. Expected: %s, Actual: %s, Error: %s', $this->expectedResponseCode, $response->getStatusCode(), $response->getReasonPhrase());
                throw new FailedRequestException($message);
            }
        }

        return $response;
    }

    /**
     * Make a GET request to the API
     *
     * @param string $uri URI relative to base URI
     * @param array $options
     * @return ResponseInterface
     * @throws FailedRequestException
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
    public function parseJsonResponse(ResponseInterface $response) : array
    {
        $data = json_decode($response->getBody()->__toString(), true);
        if ($data !== false) {
            return $data;
        }

        throw new FailedRequestException('Cannot parse JSON response body');
    }
}
