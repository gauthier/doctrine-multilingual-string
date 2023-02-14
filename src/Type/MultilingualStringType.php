<?php


namespace Gauthier\MultilingualString\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use Gauthier\MultilingualString\MultilingualString;

class MultilingualStringType extends JsonType
{

    public function getName() : string
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
        $translations = array_filter($value->extract());

        return parent::convertToDatabaseValue($translations, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) : mixed
    {

        $value = new MultilingualString(json_decode($value, true));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $value;
    }

}
