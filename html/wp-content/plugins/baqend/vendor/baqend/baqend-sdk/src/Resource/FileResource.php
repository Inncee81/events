<?php

namespace Baqend\SDK\Resource;

use Baqend\SDK\Client\RestClientInterface;
use Baqend\SDK\Exception\NeedsAuthorizationException;
use Baqend\SDK\Exception\ResponseException;
use Baqend\SDK\Model\File;
use Symfony\Component\Serializer\Serializer;
use Baqend\SDK\Service\IOService;
use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;

/**
 * Class FileResource created on 25.07.17.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\SDK\Resource
 */
class FileResource extends AbstractRestResource
{

    const TAR_GZ = '.tar.gz';
    const TAR_XZ = '.tar.xz';
    const ZIP = '.zip';

    /**
     * @var IOService
     */
    private $ioService;

    public function __construct(RestClientInterface $client, Serializer $serializer, IOService $ioService) {
        parent::__construct($client, $serializer);
        $this->ioService = $ioService;
    }

    /**
     * Downloads the contents of a file.
     *
     * @param string|File $fileId The file id or a file to download the contents of.
     * @param string $eTag An optional ETag for version control.
     * @return File The File with the downloaded content and meta data.
     * @throws NeedsAuthorizationException When the user is not privileged to download the given file.
     */
    public function download($fileId, $eTag = null) {
        if ($fileId instanceOf File) {
            return $this->download($fileId->getId(), $eTag);
        }

        $request =
            $this->getClient()->createRequest()
                ->asGet()
                ->withPath($fileId)
                ->withIfNoneMatch($eTag)
                ->build();

        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('GET', $fileId);
        }

        if ($response->getStatusCode() >= 400) {
            throw new NeedsAuthorizationException('GET', $fileId);
        }

        $file = new File($this->getClient()->getEndpoint()->__toString());
        $file->setId($fileId);
        $file->setETag(substr($response->getHeaderLine('etag'), 1, -1));
        $file->setLastModified(new \DateTime($response->getHeaderLine('last-modified')));

        if ($response->getStatusCode() !== 304) {
            $file->setBody($response->getBody());
        }

        return $file;
    }

    /**
     * Uploads the contents of a file.
     *
     * @param File $file               The file to upload the contents to.
     * @param StreamInterface $content The contents of the file to upload.
     * @return File A new file reference.
     * @throws NeedsAuthorizationException When the user is not privileged to upload the given file.
     */
    public function upload(File $file, StreamInterface $content) {
        $request = $this->sendStream('PUT', $file->getId(), $content);
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('PUT', $file->getId());
        }

        if ($response->getStatusCode() >= 400) {
            throw new NeedsAuthorizationException('PUT', $file->getId());
        }

        return $this->receiveJson($response, File::class);
    }

    /**
     * Uploads the contents of a file.
     *
     * @param File $file       The file to upload the contents to.
     * @param string $filename The name of the file to upload.
     * @return File            A new file reference.
     * @throws NeedsAuthorizationException When the user is not privileged to upload the given file.
     */
    public function uploadFile(File $file, $filename) {
        $stream = new LazyOpenStream($filename, 'r');
        try {
            return $this->upload($file, $stream);
        } finally {
            $stream->close();
        }
    }

    /**
     * Downloads the contents of a bucket as an archive.
     *
     * @param string $bucket The bucket to download the contents of.
     * @param string $type   The archive type to use.
     * @return StreamInterface A stream containing the file archive.
     * @throws NeedsAuthorizationException When the user is not privileged to download the given bucket.
     */
    public function downloadArchive($bucket, $type = self::TAR_GZ) {
        $path = "/file/$bucket$type";
        $request = $this->sendJson('GET', $path);
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('GET', $path);
        }

        if ($response->getStatusCode() >= 400) {
            throw new NeedsAuthorizationException('GET', $path);
        }

        return $response->getBody();
    }

    /**
     * Uploads the contents of a bucket as an archive.
     *
     * @param string $bucket           The file to upload the contents to.
     * @param StreamInterface $content The contents of the file to upload.
     * @param string $type             The archive type to use.
     * @return int The number of files uploaded.
     * @throws NeedsAuthorizationException When the user is not privileged to upload the given bucket.
     */
    public function uploadArchive($bucket, StreamInterface $content, $type = self::TAR_GZ) {
        $path = "/file/$bucket$type";
        $request = $this->sendStream('POST', $path, $content);
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('POST', $path);
        }

        if ($response->getStatusCode() >= 400) {
            throw new NeedsAuthorizationException('POST', $path);
        }

        return (int) $this->receiveJson($response)['writtenFiles'];
    }

    /**
     * Uploads the contents of a bucket as an archive.
     *
     * @param string $bucket   The file to upload the contents to.
     * @param string $filename The name of the file to upload.
     * @param string $type     The archive type to use.
     * @return int The number of files uploaded.
     * @throws NeedsAuthorizationException When the user is not privileged to upload the given bucket.
     */
    public function uploadArchiveFile($bucket, $filename, $type = self::TAR_GZ) {
        $stream = new LazyOpenStream($filename, 'r');
        try {
            return $this->uploadArchive($bucket, $stream, $type);
        } finally {
            $stream->close();
        }
    }

    /**
     * Finds a file by its ID.
     *
     * @param string $fileId The file ID to look for.
     * @return File|null Returns the file found or null otherwise.
     * @throws NeedsAuthorizationException When the user is not privileged to find a file in the given bucket.
     */
    public function findFile($fileId) {
        $request = $this->sendJson('HEAD', $fileId);
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('HEAD', $fileId);
        }

        if ($response->getStatusCode() === 404) {
            return null;
        }

        if ($response->getStatusCode() === 466) {
            throw new NeedsAuthorizationException('HEAD', $fileId);
        }

        $file = new File($this->getClient()->getEndpoint()->__toString());
        $file->setId($fileId);
        $file->setETag(substr($response->getHeaderLine('etag'), 1, -1));
        $file->setLastModified(new \DateTime($response->getHeaderLine('last-modified')));

        return $file;
    }

    /**
     * Deletes a file.
     *
     * @param File $file The file to delete.
     * @return void
     * @throws NeedsAuthorizationException When the user is not privileged to delete a file in the given bucket.
     */
    public function deleteFile(File $file) {
        $request = $this->sendJson('DELETE', $file->getId());
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('DELETE', $file->getId());
        }

        if ($response->getStatusCode() !== 204) {
            throw new NeedsAuthorizationException('DELETE', $file->getId());
        }
    }

    /**
     * Lists files of a bucket.
     *
     * @param string $bucket Name of the bucket to list files of.
     * @param string $path   The path root to start.
     * @param string $start  The element to start retrieving files from.
     * @param int $count     The maximum amount of files to get.
     * @param boolean $deep  If true, also include subdirectories.
     * @return File[] The file metadata retrieved.
     * @throws NeedsAuthorizationException When the user is not privileged to list files in the given bucket.
     */
    public function listFiles($bucket, $path = '/', $start = '', $count = -1, $deep = false) {
        $query = ['path' => $path, 'start' => $start, 'count' => $count, 'deep' => $deep];
        $requestPath = "/file/$bucket/ids";
        $request = $this->sendQuery('GET', $requestPath, $query);

        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('GET', $requestPath);
        }

        if ($response->getStatusCode() !== 200) {
            throw new NeedsAuthorizationException('GET', $requestPath);
        }

        return $this->receiveJson($response, File::class . '[]');
    }

    /**
     * Crates a new file bucket.
     *
     * @param string $bucket The name of the bucket to create.
     * @param array $acl     The ACL to set oin the bucket.
     * @return void
     * @throws NeedsAuthorizationException When the user is not privileged to create a bucket.
     */
    public function createBucket($bucket, array $acl = ['load' => null]) {
        $path = "/file/$bucket";
        $request = $this->sendJson('PUT', $path, $acl);
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('PUT', $path);
        }

        if ($response->getStatusCode() !== 204) {
            throw new NeedsAuthorizationException('PUT', $path);
        }
    }

    /**
     * Deletes an existing file bucket.
     *
     * @param string $bucket The name of the bucket to delete.
     * @return void
     * @throws NeedsAuthorizationException When the user is not privileged to delete the bucket.
     */
    public function deleteBucket($bucket) {
        $path = "/file/$bucket";
        $request = $this->sendJson('DELETE', $path);
        try {
            $response = $this->execute($request);
        } catch (ResponseException $exception) {
            throw new NeedsAuthorizationException('DELETE', $path);
        }

        if ($response->getStatusCode() !== 204) {
            throw new NeedsAuthorizationException('DELETE', $path);
        }
    }

    /**
     * Create a random file in a bucket.
     *
     * @param string $bucket
     * @return File The file created.
     * @throws ResponseException When no such file could be created.
     */
    public function createRandomFile($bucket) {
        $request = $this->sendJson('POST', "/file/$bucket");
        $response = $this->execute($request);

        if ($response->getStatusCode() !== 201) {
            throw new ResponseException($response);
        }

        return $this->receiveJson($response, File::class);
    }
}
