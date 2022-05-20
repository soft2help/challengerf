<?php
namespace App\Helpers\Encoder;

use App\Helpers\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * Encodes JSON data.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class JsonEncoder implements EncoderInterface, DecoderInterface
{
    const FORMAT = 'json';

    protected $encodingImpl;
    protected $decodingImpl;

    public function __construct(JsonEncode $encodingImpl = null, JsonDecode $decodingImpl = null){
        $this->encodingImpl = $encodingImpl ?: new JsonEncode();
        $this->decodingImpl = $decodingImpl ?: new JsonDecode([JsonDecode::ASSOCIATIVE => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = []){
        
        

        return $this->encodingImpl->encode($data, self::FORMAT, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = []){
        return $this->decodingImpl->decode($data, self::FORMAT, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format){
        return self::FORMAT === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding($format){
        return self::FORMAT === $format;
    }
}
