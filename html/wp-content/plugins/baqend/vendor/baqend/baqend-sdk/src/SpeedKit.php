<?php

namespace Baqend\SDK;

use Baqend\SDK\Client\ClientInterface;
use Baqend\SDK\Client\RequestBuilder;
use Baqend\SDK\Exception\ResponseException;
use Baqend\SDK\Model\SpeedKitInfo;
use Baqend\SDK\Resource\AbstractResource;
use Symfony\Component\Serializer\Serializer;
use Baqend\SDK\Service\IOService;

/**
 * Class SpeedKit created on 09.08.17.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\SDK\Resource
 */
class SpeedKit extends AbstractResource
{

    /**
     * @var IOService
     */
    private $ioService;

    public function __construct(ClientInterface $client, IOService $ioService, Serializer $serializer) {
        parent::__construct($client, $serializer);
        $this->ioService = $ioService;
    }

    /**
     * @param string $swFilename      Filename of the service worker.
     * @param string $snippetFilename Filename of the snippet.
     * @param string $version         An optional version number to use.
     * @return SpeedKitInfo           An object containing information about
     * @throws ResponseException      When the server did not respond.
     */
    public function createInfo($swFilename, $snippetFilename, $version = 'latest') {
        if (file_exists($swFilename)) {
            $localContent = $this->ioService->convertLineSeparatorsToLf(file_get_contents($swFilename));
            $localVersion = $this->extractServiceWorkerVersion($localContent);

            // Calculate ETags
            $localETag = $this->ioService->calculateEntityTag($swFilename, IOService::DEFAULT_CHUNK_SIZE, true);
        } else {
            $localContent = null;
            $localVersion = null;
            $localETag = null;
        }

        if (file_exists($snippetFilename)) {
            $localSnippet = $this->ioService->convertLineSeparatorsToLf(file_get_contents($snippetFilename));
        } else {
            $localSnippet = null;
        }

        $request = $this->createRequest()
            ->withPath('/'.$version.'/sw.js')
            ->withIfNoneMatch($localETag)
            ->build();

        $response = $this->execute($request);

        $isLatest = $response->getStatusCode() === 304;
        $isOutdated = $response->getStatusCode() === 200;
        if (!$isLatest && !$isOutdated) {
            throw new ResponseException($response);
        }

        if ($isLatest) {
            $swContent = $localContent;
            $snippetContent = $localSnippet;
            $remoteVersion = $localVersion;
            $remoteETag = $localETag;
        } else {
            $swContent = $response->getBody()->getContents();
            $remoteVersion = $this->extractServiceWorkerVersion($swContent);
            $remoteETag = substr($response->getHeaderLine('etag'), 1, -1);

            $snippetRequest = $this->createRequest()
                ->withPath('/'.$version.'/snippet.js')
                ->withIfNoneMatch($localETag)
                ->build();

            $snippetResponse = $this->execute($snippetRequest);
            $snippetContent = $snippetResponse->getBody()->getContents();
        }

        return new SpeedKitInfo(
            [
                'swContent' => $swContent,
                'snippetContent' => $snippetContent,
                'localVersion' => $localVersion,
                'localETag' => $localETag,
                'remoteVersion' => $remoteVersion,
                'remoteETag' => $remoteETag,
                'isLatest' => $isLatest,
                'isOutdated' => $isOutdated,
            ]
        );
    }

    /**
     * Returns the service worker file.
     *
     * @param string $version    An optional version number to use.
     * @return string            The file contents.
     * @throws ResponseException When the server did not respond successfully.
     */
    public function getServiceWorker($version = 'latest') {
        return $this->getFile('sw.js', $version);
    }

    /**
     * Returns the snippet file.
     *
     * @param string $version    An optional version number to use.
     * @return string            The file contents.
     * @throws ResponseException When the server did not respond successfully.
     */
    public function getSnippet($version = 'latest') {
        return $this->getFile('snippet.js', $version);
    }

    /**
     * Compares a local service worker with the remote version.
     *
     * @param string $swFilename The filename of the local service worker.
     * @param string $version    An optional version number to use.
     * @return bool              True, if local service worker is outdated.
     * @throws ResponseException When the server did not respond successfully.
     */
    public function isServiceWorkerOutdated($swFilename, $version = 'latest') {
        $localETag = $this->ioService->calculateEntityTag($swFilename, IOService::DEFAULT_CHUNK_SIZE, true);
        $remoteETag = $this->getServiceWorkerETag($version);

        return $localETag !== $remoteETag;
    }

    /**
     * Compares a local snippet with the remote version.
     *
     * @param string $snippetFilename The filename of the local snippet.
     * @param string $version         An optional version number to use.
     * @return bool True, if local snippet is outdated.
     * @throws ResponseException When the server did not respond successfully.
     */
    public function isSnippetOutdated($snippetFilename, $version = 'latest') {
        $localETag = $this->ioService->calculateEntityTag($snippetFilename, IOService::DEFAULT_CHUNK_SIZE, true);
        $remoteETag = $this->getSnippetETag($version);

        return $localETag !== $remoteETag;
    }

    /**
     * Returns the service worker version.
     *
     * @param string $version An optional version number to use.
     * @return string
     * @throws ResponseException
     */
    public function getServiceWorkerETag($version = 'latest') {
        return $this->getFileETag('sw.js', $version);
    }

    /**
     * Returns the snippet version.
     *
     * @param string $version An optional version number to use.
     * @return string
     * @throws ResponseException When the server did not respond successfully.
     */
    public function getSnippetETag($version = 'latest') {
        return $this->getFileETag('snippet.js', $version);
    }

    /**
     * @param string $file       The file to check.
     * @param string $version    An optional version number to use.
     * @return string            The file's content.
     * @throws ResponseException When the server did not respond successfully.
     */
    private function getFile($file, $version = 'latest') {
        $request = $this->createRequest()->withPath('/'.$version.'/'.$file)->build();
        $response = $this->execute($request);

        return $response->getBody()->getContents();
    }

    /**
     * @param string $file       The file to check.
     * @param string $version    An optional version number to use.
     * @return string            The file's entity tag.
     * @throws ResponseException When the server did not respond successfully.
     */
    private function getFileETag($file, $version = 'latest') {
        $request = $this->createRequest()->asHead()->withPath('/'.$version.'/'.$file)->build();
        $response = $this->execute($request);

        $etag = substr($response->getHeaderLine('etag'), 1, -1);

        return $etag;
    }

    /**
     * @return RequestBuilder
     */
    private function createRequest() {
        return (new RequestBuilder($this->serializer))
            ->withHost('www.baqend.com')
            ->withScheme('https')
            ->withBasePath('/speed-kit');
    }

    /**
     * @param string $swContent
     * @return string|null
     */
    private function extractServiceWorkerVersion($swContent) {
        if (preg_match('#^/\* ! speed-kit ([\w.-]+) |#', $swContent, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
