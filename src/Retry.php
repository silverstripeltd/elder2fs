<?php

namespace Elder2Fs;

class Retry
{
    public static function create_decider(\Psr\Log\LoggerInterface $logger = null)
    {
        return function (
            $try,
            \GuzzleHttp\Psr7\Request $request,
            \GuzzleHttp\Psr7\Response $response = null,
            \GuzzleHttp\Exception\RequestException $e = null
        ) use ($logger) {
            // 18 retries = max ~4 minutes delay (last delay, 2mins). This excludes the actual request time.
            if ($try >= 18) {
                return false;
            }

            if ($e instanceof \GuzzleHttp\Exception\ConnectException) {
                if ($logger) $logger->warn(sprintf('Failed try %d: "%s"', $try+1, $e->getMessage()));
                return true;
            }

            if ($response) {
                if ($response->getStatusCode() >= 500) {
                    if ($logger) {
                        $logger->warn(
                            sprintf('Failed try %d: status code "%d"', $try+1, $response->getStatusCode())
                        );
                    }
                    return true;
                }
            }

            return false;
        };
    }
}
