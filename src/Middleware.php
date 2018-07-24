<?php

namespace Elder2Fs;

use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\RequestInterface;

class Middleware
{
    public static function flush()
    {
        $token = sha1(getenv('PATH') . getenv('PWD') . getenv('USER') . bin2hex(openssl_random_pseudo_bytes(20)));

        return function (callable $handler) use ($token) {
            return function (RequestInterface $request, array $options) use ($handler, $token) {
                if (in_array('application/json', $request->getHeader('Content-Type'))) {
                    $b = json_decode($request->getBody(), true);
                    $b['flushToken'] = $token;
                    $request = $request->withBody(stream_for(json_encode($b)));
                }
                return $handler($request, $options);
            };
        };
    }
}
