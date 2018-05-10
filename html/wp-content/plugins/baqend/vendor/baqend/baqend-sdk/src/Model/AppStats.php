<?php

namespace Baqend\SDK\Model;

/**
 * Class AppStats created on 17.08.17.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\SDK\Model
 */
class AppStats
{

    /**
     * @var int
     */
    private $download;

    /**
     * @var int
     */
    private $requests;

    /**
     * @var int
     */
    private $dbSpace;

    /**
     * @var int
     */
    private $fileSpace;

    public function __construct(array $data) {
        $this->download = $data['download'];
        $this->requests = $data['requests'];
        $this->dbSpace = $data['dbSpace'];
        $this->fileSpace = $data['fileSpace'];
    }

    /**
     * @return int
     */
    public function getDownload() {
        return $this->download;
    }

    /**
     * @return int
     */
    public function getRequests() {
        return $this->requests;
    }

    /**
     * @return int
     */
    public function getDbSpace() {
        return $this->dbSpace;
    }

    /**
     * @return int
     */
    public function getFileSpace() {
        return $this->fileSpace;
    }
}
