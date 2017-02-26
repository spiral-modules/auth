<?php
/**
 * spiral
 *
 * @author    Wolfy-J
 */

namespace Spiral\Tests;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequest;

abstract class HttpTest extends BaseTest
{
    protected function get(
        $uri,
        array $query = [],
        array $headers = [],
        array $cookies = []
    ): ResponseInterface {
        return $this->http->perform($this->request($uri, 'GET', $query, $headers, $cookies));
    }

    protected function post(
        $uri,
        array $data = [],
        array $headers = [],
        array $cookies = []
    ): ResponseInterface {
        return $this->http->perform(
            $this->request($uri, 'POST', [], $headers, $cookies)->withParsedBody($data)
        );
    }

    protected function request(
        $uri,
        string $method,
        array $query = [],
        array $headers = [],
        array $cookies = []
    ): ServerRequest {
        return new ServerRequest(
            [],
            [],
            $uri,
            $method,
            'php://input',
            $headers, $cookies,
            $query
        );
    }

    protected function fetchCookies(array $header)
    {
        $result = [];

        foreach ($header as $line) {
            $cookie = explode('=', $line);
            $result[$cookie[0]] = rawurldecode(substr($cookie[1], 0, strpos($cookie[1], ';')));
        }

        return $result;
    }
}