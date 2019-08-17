<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\FileSystem;

use FilipSedivy\EET\Exceptions\UnexpectedException;

class FileNotFoundException extends UnexpectedException implements FileSystemException
{
}
