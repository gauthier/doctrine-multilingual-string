<?php


namespace Gauthier\MultilingualString\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use Gauthier\MultilingualString\MultilingualString;

class MultilingualStringType extends JsonType
{

    public function getName()
    {
        return 'multilingual';
    }

    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return false|mixed|string|null
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // filter empty translations
        $value = array_filter($value);
        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * @param string $value
     * @param AbstractPlatform $platform
     * @return array|MultilingualString|mixed|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = new MultilingualString(json_decode($value, true));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $value;
    }


}
