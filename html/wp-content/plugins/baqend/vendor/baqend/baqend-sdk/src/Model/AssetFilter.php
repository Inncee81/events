<?php

namespace Baqend\SDK\Model;

/**
 * Class AssetFilter created on 25.07.17.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\SDK\Model
 */
class AssetFilter implements \JsonSerializable
{

    const DOCUMENT = 'document';
    const STYLE = 'style';
    const SCRIPT = 'script';
    const FEED = 'feed';
    const AUDIO = 'audio';
    const VIDEO = 'video';
    const TRACK = 'track';
    const IMAGE = 'image';
    const FONT = 'font';

    /**
     * @var string|null
     */
    private $query;

    /**
     * @var string[]
     */
    private $prefixes;

    /**
     * @var string[]
     */
    private $mediaTypes;

    /**
     * @var string[]
     */
    private $contentTypes;

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->setQuery(isset($data['query']) ? $data['query'] : null);
        $this->setPrefixes(isset($data['prefixes']) ? $data['prefixes'] : []);
        $this->setMediaTypes(isset($data['mediaTypes']) ? $data['mediaTypes'] : []);
        $this->setContentTypes(isset($data['contentTypes']) ? $data['contentTypes'] : []);
    }

    /**
     * @return null|string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @param null|string $query
     */
    public function setQuery($query) {
        $this->query = $query;
    }

    /**
     * @return string[]
     */
    public function getPrefixes() {
        return $this->prefixes;
    }

    /**
     * @param string[] $prefixes
     */
    public function setPrefixes(array $prefixes) {
        $this->prefixes = $prefixes;
    }

    /**
     * @param string $prefix
     */
    public function addPrefix($prefix) {
        $this->prefixes[] = $prefix;
    }

    /**
     * @param string[] $prefixes
     */
    public function addPrefixes(array $prefixes) {
        $this->prefixes = array_merge($this->prefixes, $prefixes);
    }

    /**
     * @return string[]
     */
    public function getMediaTypes() {
        return $this->mediaTypes;
    }

    /**
     * @param string[] $mediaTypes
     */
    public function setMediaTypes(array $mediaTypes) {
        $this->mediaTypes = $mediaTypes;
    }

    /**
     * @param string $mediaType
     */
    public function addMediaType($mediaType) {
        $this->mediaTypes[] = $mediaType;
    }

    /**
     * @param string[] $mediaTypes
     */
    public function addMediaTypes(array $mediaTypes) {
        $this->mediaTypes = array_merge($this->mediaTypes, $mediaTypes);
    }

    /**
     * @return string[]
     */
    public function getContentTypes() {
        return $this->contentTypes;
    }

    /**
     * @param string[] $contentTypes
     */
    public function setContentTypes(array $contentTypes) {
        $this->contentTypes = $contentTypes;
    }

    /**
     * @param string $contentType
     */
    public function addContentType($contentType) {
        $this->contentTypes[] = $contentType;
    }

    /**
     * @param string[] $contentTypes
     */
    public function addContentTypes(array $contentTypes) {
        $this->contentTypes = array_merge($this->contentTypes, $contentTypes);
    }

    public function jsonSerialize() {
        $response = [];
        if ($this->query) {
            $response['query'] = $this->query;
        }
        if ($this->prefixes) {
            $response['prefixes'] = $this->prefixes;
        }
        if ($this->mediaTypes) {
            $response['mediaTypes'] = $this->mediaTypes;
        }
        if ($this->contentTypes) {
            $response['contentTypes'] = $this->contentTypes;
        }

        return $response;
    }
}
