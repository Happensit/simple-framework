<?php

namespace Commty\Simple\Reader;

/**
 * Class AbstractFileReader
 * @package commty\Reader
 */
abstract class AbstractFileReader implements FileReaderInterface
{
    /**
     * @param string $filepath
     * @return array
     */
    public function asArray($filepath)
    {
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }

    /**
     * @param string $filepath
     * @return string
     */
    public function asString($filepath)
    {
        return file_get_contents($filepath);
    }
}
