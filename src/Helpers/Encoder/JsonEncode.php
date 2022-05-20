<?php
namespace App\Helpers\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * Encodes JSON data.
 *
 * @author Sander Coolen <sander@jibber.nl>
 */
class JsonEncode implements EncoderInterface
{
    const OPTIONS = 'json_encode_options';

    private $defaultContext = [
        self::OPTIONS => 0,
    ];

    /**
     * @param array $defaultContext
     */
    public function __construct($defaultContext = []){
        if (!\is_array($defaultContext)) {
            @trigger_error(sprintf('Passing an integer as first parameter of the "%s()" method is deprecated since Symfony 4.2, use the "json_encode_options" key of the context instead.', __METHOD__), E_USER_DEPRECATED);

            $this->defaultContext[self::OPTIONS] = (int) $defaultContext;
        } else {
            $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
        }
    }


    public function walk($array,$camposordenados){
        $nuevo=[];
        foreach($camposordenados as $campo)
            if(array_key_exists($campo,$array))
                $nuevo[$campo]= $array[$campo];

        foreach($array as $key=>$value){
            if(!in_array($key,$camposordenados))
                $nuevo[$key]=$value;

            if(is_array($value)){
                $nuevo[$key]=$this->walk($value,$camposordenados);
            }else{
                $nuevo[$key]=$value;
            }

        }

        return $nuevo;
    }

    /**
     * Encodes PHP data to a JSON string.
     *
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = []){
        
        $data=$this->walk($data,["id","descripcion"]);
            
        

        $options = $context[self::OPTIONS] ?? $this->defaultContext[self::OPTIONS];

        try {
            $encodedJson = json_encode($data, $options);
        } catch (\JsonException $e) {
            throw new NotEncodableValueException($e->getMessage(), 0, $e);
        }

        if (\PHP_VERSION_ID >= 70300 && (JSON_THROW_ON_ERROR & $options)) {
            return $encodedJson;
        }

        if (JSON_ERROR_NONE !== json_last_error() && (false === $encodedJson || !($options & JSON_PARTIAL_OUTPUT_ON_ERROR))) {
            throw new NotEncodableValueException(json_last_error_msg());
        }

        return $encodedJson;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format){
        return JsonEncoder::FORMAT === $format;
    }
}
