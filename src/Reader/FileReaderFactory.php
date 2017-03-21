<?php

namespace Commty\Simple\Reader;

/**
 * Class FileReaderFactory
 * @package commty\Reader
 */
class FileReaderFactory
{
    /**
     * @var null
     */
    public static $reader = [];

    /**
     * @var array
     * Registered config file extensions.
     */
    protected static $extensions = [
        'env' => EnvFileReader::class,
        'csv' => CsvFileReader::class,
        'php' => PhpFileReader::class
    ];

    /**
     * @param $file
     * @param string $method
     * @return array
     * @throws FileReaderException
     */
    public static function getFileContent($file, $method = 'asArray')
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new FileReaderException(sprintf(
                "File '%s' doesn't exist or not readable",
                $file
            ));
        }

        $pathinfo = pathinfo($file);

        if (!isset($pathinfo['extension'])) {
            throw new FileReaderException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $file
            ));
        }

        $extension = strtolower($pathinfo['extension']);

        if (isset(static::$extensions[$extension])) {
            $reader = self::getReader(static::$extensions[$extension]);
            if (!$reader instanceof FileReaderInterface) {
                static::$extensions[$extension] = $reader;
            }

            /* @var FileReaderInterface $reader */
            $content = call_user_func([$reader, $method], $file);

        } else {
            throw new FileReaderException(sprintf(
                'Unsupported file extension: .%s',
                $pathinfo['extension']
            ));
        }

        return $content;
    }

    /**
     * Get Reader Class
     * @param $reader
     * @return mixed
     */
    public static function getReader($reader)
    {
        if (!isset(static::$reader[$reader])) {
            static::$reader[$reader] = new $reader();
        }

        return static::$reader[$reader];
    }
}
