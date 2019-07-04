<?php
declare(strict_types=1);

namespace Studio24\Frontend\Utils;

use Studio24\Frontend\Exception\RedirectException;

/**
 * Class to manage redirects
 *
 * @package Studio24\Frontend\Utils
 */
class Redirects
{
    const SOURCE = 0;
    const DESTINATION = 1;
    const HTTP_STATUS = 2;

    /**
     * Default HTTP status code
     *
     * @var int
     */
    protected $defaultHttpStatus = 301;

    /**
     * Array of simple redirects with one to one mapping (source => destination)
     * @var array
     */
    protected $redirectsOneToOne = [];

    /**
     * Array of redirects which use * to match any character (source => destination)
     *
     * @var array
     */
    protected $redirectRegex = [];

    /**
     * Destination URL for the last matched redirect
     *
     * @var string
     */
    protected $lastDestination;

    /**
     * HTTP status code for the last matched redirect
     *
     * @var int
     */
    protected $lastHttpStatus;

    /**
     * Loads redirects from a CSV file
     *
     * CSV file needs two columns at a minimum (source, destination) and can optionally have three columns (source, destination, redirect code)
     *
     * @param string $file
     * @throws RedirectException
     */
    public function loadFromCsv(string $file)
    {
        if (!file_exists($file)) {
            throw new RedirectException(sprintf('File not found at %s', $file));
        }

        if (($handle = fopen($file, "r")) !== false) {
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                $count = count($row);
                if ($count == 2) {
                    $this->addRedirect($row[self::SOURCE], $row[self::DESTINATION]);
                } elseif (count($row) == 3) {
                    $this->addRedirect($row[self::SOURCE], $row[self::DESTINATION], (int) $row[self::HTTP_STATUS]);
                } else {
                    throw new RedirectException(sprintf('CSV file needs at least two columns for source URL and destination URL, %s columns found', count($row)));
                }
            }
            fclose($handle);
        }
    }

    /**
     * Add redirect
     *
     * @param string $source Source URL
     * @param string $destination Destination URL
     * @param int|null $httpCode HTTP status code (must be a 3xx code), defaults to 301
     */
    public function addRedirect(string $source, string $destination, ?int $httpCode = null)
    {
        $redirect = [];
        $redirect[self::SOURCE] = filter_var($source, FILTER_SANITIZE_URL);
        $redirect[self::DESTINATION] = filter_var($destination, FILTER_SANITIZE_URL);

        if ($httpCode !== null && (substr((string) $httpCode, 0, 1) === '3')) {
            $redirect[self::HTTP_STATUS] = (int) $httpCode;
        }

        if (strpos($source, '*') !== false) {
            $this->redirectRegex[] = $redirect;
        } else {
            $this->redirectsOneToOne[] = $redirect;
        }
    }

    /**
     * Match a source URL with a redirect
     *
     * The * character has special meaning:
     *  - When * used in source it matches one or more characters that can then be used as params in the redirect URL
     *  - When * used in source and destination then the URL that matches the * in source is copied to the destination URL
     *
     * You can then get the destination URL via getLastDestination() and the HTTP status code via getLastHttpStatus()
     *
     * @param string $url URL to match against redirects
     * @return bool
     */
    public function match(string $url): bool
    {
        // Try one to one match first
        $matchOne = function($url) {
            return array_search($url, array_column($this->redirectsOneToOne, self::SOURCE));
        };
        $result = $matchOne($url);

        // Try to remove/add trailing slash from URL
        if ($result === false) {
            if (substr($url, strlen($url)-1, 1) === '/') {
                $result = $matchOne(substr($url, 0,strlen($url)-1));
            } else {
                $result = $matchOne($url . '/');
            }
        }

        if ($result !== false) {
            $redirect = $this->redirectsOneToOne[$result];
            $this->setLastDestination($redirect[self::DESTINATION]);
            if (isset($redirect[self::HTTP_STATUS])) {
                $this->setLastHttpStatus($redirect[self::HTTP_STATUS]);
            } else {
                $this->setLastHttpStatus($this->defaultHttpStatus);
            }
            return true;
        }

        // Try regex match
        foreach ($this->redirectRegex as $item) {
            if (preg_match($this->regex($item[self::SOURCE]), $url, $m)) {
                // Leave $m with only matches for *
                array_shift($m);

                $this->setLastDestination($this->replace($item[self::DESTINATION], $m));
                if (isset($redirect[self::HTTP_STATUS])) {
                    $this->setLastHttpStatus($item[self::HTTP_STATUS]);
                } else {
                    $this->setLastHttpStatus($this->defaultHttpStatus);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Return regex for use in matching URLs
     *
     * @param string $source
     * @return string
     */
    public function regex(string $source)
    {
        return '!^' . str_replace('\*', '(.+)', preg_quote($source, '!')) . '$!';
    }

    /**
     * Replace matched params into a redirect destination
     *
     * E.g. for destination URL /news/* and param 1234 returned string = /news/1234
     * This can replace multiple params into the destination URL
     * Any missing params are removed so there are no * characters left in the URL
     *
     * @param string $destination Destination redirect, must contain '*' characters to replace params into
     * @param array $replace Array of parameters to replace into string
     * @return string
     */
    public function replace(string $destination, array $replace)
    {
        $redirect = '';
        $x = 0;

        // If no * in destination, just return it
        if (strpos($destination, '*') === false) {
            return $destination;
        }

        // Check if * is at start
        if (strpos($destination, '*') === 0) {
            $redirect .= $replace[0];
            $x++;
        }

        // Replace params, one by one
        $token = strtok($destination, '*');
        do {
            $redirect .= $token;

            // Add param, if it exists
            if (isset($replace[$x])) {
                $redirect .= $replace[$x];
                $x++;
            }
        } while ($token = strtok('*'));

        return $redirect;
    }

    /**
     * Return array of all redirects setup
     *
     * 0 = Source
     * 1 = Destination
     * 2 = Redirect code (default = 301)
     *
     * @return array
     */
    public function getRedirects(): array
    {
        return array_merge($this->redirectsOneToOne, $this->redirectRegex);
    }

    /**
     * Return destination URL for the last matched redirect
     * @return string
     */
    public function getLastDestination(): string
    {
        return $this->lastDestination;
    }

    /**
     * Set destination URL for the last matched redirect
     * @param string $lastDestination
     */
    public function setLastDestination(string $lastDestination): void
    {
        $this->lastDestination = $lastDestination;
    }

    /**
     * Return HTTP status code for the last matched redirect
     * @return int
     */
    public function getLastHttpStatus(): int
    {
        return $this->lastHttpStatus;
    }

    /**
     * Set HTTP status code for the last matched redirect
     * @param int $lastHttpStatus
     */
    public function setLastHttpStatus(int $lastHttpStatus): void
    {
        $this->lastHttpStatus = $lastHttpStatus;
    }
}
