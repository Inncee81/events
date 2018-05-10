<?php

namespace Baqend\SDK\Serializer;

use Baqend\SDK\Model\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class FileNormalizer created on 30.01.2018.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\SDK\Serializer
 */
class FileNormalizer implements NormalizerInterface, DenormalizerInterface
{

    const ENDPOINT = 'endpoint';

    /**
     * @var DateTimeNormalizer
     */
    private $dateTimeNormalizer;

    /**
     * FileNormalizer constructor.
     *
     * @param DateTimeNormalizer $dateTimeNormalizer
     */
    public function __construct(DateTimeNormalizer $dateTimeNormalizer) {
        $this->dateTimeNormalizer = $dateTimeNormalizer;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()) {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('The data must be an array.');
        }

        $file = new File($context[self::ENDPOINT]);
        $file->setId($data['id']);
        $file->setETag($data['eTag']);
        $file->setLastModified($this->dateTimeNormalizer->denormalize($data['lastModified'], \DateTime::class));

        return $file;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null) {
        return $type === File::class;
    }

    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = array()) {
        if (!$object instanceof File) {
            throw new \InvalidArgumentException('The object must be an instance of "'.File::class.'".');
        }

        return [
            'id' => $object->getId(),
            'eTag' => $object->getETag(),
            'lastModified' => $this->dateTimeNormalizer->normalize($object->getLastModified()),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null) {
        return $data instanceof File;
    }
}
