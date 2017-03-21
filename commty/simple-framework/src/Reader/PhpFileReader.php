<?php

namespace Commty\Simple\Reader;

/**
 * Class PhpFileReader
 * @package commty\Reader
 */
class PhpFileReader extends AbstractFileReader
{
    /**
     * @param string $filepath
     * @param array $data
     * @return string
     */
    public function render($filepath, array $data = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($data, EXTR_OVERWRITE);
        require $filepath;
        return ob_get_clean();
    }
}
