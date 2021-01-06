<?php

/**
 * @todo review this - can we remove this dependency? Not currently in composer.json
 */

declare(strict_types=1);

namespace Strata\Frontend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\SimpleCache\CacheInterface;

/**
 * Controller for common website URLs
 *
 * @package Strata\Frontend\Controller
 */
class WebController extends AbstractController
{

    /**
     * Robots.txt file
     *
     * @see https://github.com/h5bp/html5-boilerplate/blob/6.1.0/dist/doc/misc.md#robotstxt
     * @return Response
     */
    public function robots()
    {
        $response = $this->render('static/robots.txt');
        $response->headers->set('Content-Type', 'text/plain');
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);

        return $response;
    }

    /**
     * Web manifest file
     *
     * @return Response
     */
    public function manifest(CacheInterface $cache)
    {
        $response = $this->render('static/site.webmanifest');
        $response->headers->set('Content-Type', 'application/json');
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);

        return $response;
    }
}
