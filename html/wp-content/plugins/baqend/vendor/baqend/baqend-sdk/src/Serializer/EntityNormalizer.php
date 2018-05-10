<?php

namespace Baqend\SDK\Serializer;

use Baqend\SDK\Model\Entity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class EntityNormalizer created on 31.01.18.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\SDK\Serializer
 */
class EntityNormalizer implements NormalizerInterface
{

    /**
     * @var ObjectNormalizer
     */
    private $objectNormalizer;

    /**
     * EntityNormalizer constructor.
     *
     * @param ObjectNormalizer $objectNormalizer
     */
    public function __construct(ObjectNormalizer $objectNormalizer) {
        $this->objectNormalizer = $objectNormalizer;
    }


    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = array()) {
        if (!$object instanceof Entity) {
            throw new \InvalidArgumentException('Expected entity, but received "'.gettype($object).'".');
        }

        // Call the object normalizer
        $normalized = $this->objectNormalizer->normalize($object);

        // Remove null IDs
        if ($normalized['id'] === null) {
            unset($normalized['id']);
            unset($normalized['version']);
        }

        return $normalized;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null) {
        return $data instanceof Entity;
    }
}
