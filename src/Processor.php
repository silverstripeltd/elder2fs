<?php
namespace Elder2Fs;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class Processor
{

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $variables;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;

    /**
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param array $variables
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, array $variables, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->variables = $variables;
        $this->log = $logger;
    }

    /**
     * @param \Elder2Fs\Page $page
     */
    public function process(Page $page)
    {
        $this->log->info(sprintf('Processing "%s"' . PHP_EOL, $page->path));
        if ($page->url) {
            $u = parse_url($page->url);
            $path = sprintf('v2/article/%s%s', $u['host'], $u['path']);
        } else {
            $path = sprintf('article/%s/%s/%s', $page->version, $page->locale, $page->kb);
        }

        try {
            $response = $this->client->request('POST', $path, [
                'json' => [
                    'variables' => $this->variables,
                ]
            ]);
        } catch (ConnectException $e) {
            $this->log->error(sprintf(
                'Failed to connect to "%s%s": %s' . PHP_EOL,
                $this->client->getConfig('base_uri'),
                $path,
                $e->getMessage()
            ));
            return;
        } catch (RequestException $e) {
            $this->log->error(sprintf(
                'Failed to request "%s": "%s" (%d)' . PHP_EOL,
                $page->path,
                $e->getResponse()->getBody(),
                $e->getResponse()->getStatusCode()
            ));
            return;
        } catch (\Exception $e) {
            $this->log->error(sprintf(
                'Failed to generate "%s": "%s"' . PHP_EOL,
                $page->path,
                $e->getMessage()
            ));
            return;
        }
        $body = (string)$response->getBody();

        $md = $this->parse($body, array_merge($page->meta, ['notoc' => true]));
        file_put_contents($page->path, $md);
    }

    /**
     * @param string $body
     * @param array $extraFrontMatter
     * @return string - the parsed body
     */
    public function parse($body, array $extraFrontMatter)
    {
        $parser = new \Mni\FrontYAML\Parser();
        $document = $parser->parse($body, false);
        $mdContent = $document->getContent();
        $frontMatter = $document->getYAML();
        if (empty($frontMatter) || !is_array($frontMatter)) {
            $frontMatter = [];
        }
        $ymlFrontMatter = Yaml::dump(array_merge($frontMatter, $extraFrontMatter), 999999);
        return sprintf("---" . PHP_EOL . $ymlFrontMatter . PHP_EOL . "---" . PHP_EOL . $mdContent . PHP_EOL);
    }
}
