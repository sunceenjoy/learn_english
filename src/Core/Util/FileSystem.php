<?php

namespace Eng\Core\Util;

class FileSystem
{
    public static function mkdir($dir, $mode = 0777)
    {
        if (!is_dir($dir)) {
            mkdir($dir, $mode, true);
        }
    }
}
