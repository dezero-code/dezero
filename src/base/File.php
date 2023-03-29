<?php
/**
 * Base File class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use dezero\helpers\FileHelper;
use dezero\helpers\Number;
use Dz;
use Yii;

/**
 * File is the base class to handle filesystem objects
 */
class File extends \yii\base\BaseObject
{
    /**
     * @var string File path given as input
     */
    private $file_path;


    /**
     * @var string Real file path
     */
    private $real_path;


    /**
     * @var array Path file information
     */
    private $vec_info;


    /**
     * File constructor
     */
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
        if ( preg_match("/^\@/", $file_path) )
        {
            $this->real_path = Yii::getAlias($file_path);
        }
        else
        {
            $this->real_path = FileHelper::normalizePath($file_path);
        }

        // Clear PHP's internal stat cache
        clearstatcache();

        // Load path information
        if ( $this->exists() )
        {
            $this->info();
        }
    }


    /**
     * Load (open) a filesystem object given a path
     */
    public static function load(string $file_path) : static
    {
        return new static($file_path);
    }


    /**
     * Check if current filesystem object exists
     */
    public function exists() : bool
    {
        return file_exists($this->real_path);
    }


    /**
     * Return an array with the file information
     */
    public function info() : array
    {
        if ( $this->vec_info === null )
        {
            $this->vec_info = pathinfo($this->real_path);
            $this->vec_info['is_file'] = false;
            $this->vec_info['is_dir'] = false;

            // Check if current filesystem object is a file
            if ( is_file($this->real_path) )
            {
                $this->vec_info['is_file'] = true;
            }

            // Check if current filesystem object is a directory
            else if ( is_dir($this->real_path) )
            {
                $this->vec_info['is_dir'] = true;
            }

            // Readable?
            $this->vec_info['is_readable'] = is_readable($this->real_path);

            // Writable?
            $this->vec_info['is_writable'] = is_writable($this->real_path);
        }

        return $this->vec_info;
    }


    /**
     * Return real path for the current filesystem object
     */
    public function realPath() : ?string
    {
        return $this->real_path;
    }


    /**
     * Alias of realPath() method
     */
    public function pwd() : ?string
    {
        return $this->realPath();
    }


    /**
     * Returns the current file directory name (without final slash)
     * (eg. '/var/www/htdocs/files' for '/var/www/htdocs/files/document.pdf')
     */
    public function dirname() : ?string
    {
        return $this->getInfo('dirname');
    }

    /**
     * Returns the current file basename (file name plus extension)
     * (eg. 'document.pdf' for '/var/www/htdocs/files/document.pdf').
     */
    public function basename() : ?string
    {
        return $this->getInfo('basename');
    }


    /**
     * Returns the current file extension (without the dot symbol)
     * (eg. 'pdf' for '/var/www/htdocs/files/document.pdf')
     */
    public function extension() : ?string
    {
        return $this->getInfo('extension');
    }


    /**
     * Returns the current file name (without extension)
     * (eg. 'document' for '/var/www/htdocs/files/document.pdf')
     */
    public function filename() : ?string
    {
        return $this->getInfo('filename');
    }


    /**
     * Check if current filesystem object is a file
     */
    public function isFile() : bool
    {
        return $this->getInfo('is_file');
    }


    /**
     * Check if current filesystem object is a directory
     */
    public function isDirectory() : bool
    {
        return $this->getInfo('is_dir');
    }


    /**
     * Alias of isDirectory method
     */
    public function isDir() : bool
    {
        return $this->isDirectory();
    }


    /**
     * Check if current filesystem object is readable
     */
    public function isReadable() : bool
    {
        return $this->getInfo('is_readable');
    }


    /**
     * Check if current filesystem object is writable
     */
    public function isWritable() : bool
    {
        return $this->getInfo('is_writable');
    }


    /**
     * Return the size of current filesystem object in bytes
     */
    public function size(array $vec_options = []) : int
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return 0;
        }

        // File size
        if ( $this->isFile() )
        {
            return sprintf("%u", filesize($this->real_path));
        }

        // Directory size
        return $this->directorySize($vec_options);
    }


    /**
     * Return the size of current filesystem object in the given unit
     */
    public function formatSize(?int $value = null) : string
    {
        $vec_units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        $value = $value === null ? $this->size() : $value;

        $bytes = max($value, 0);
        $expo = floor(($bytes ? log($bytes) : 0) / log(1024));
        $expo = min($expo, count($vec_units)-1);

        $bytes /= pow(1024, $expo);

        return Number::format($bytes) . ' '. $vec_units[$expo];
    }


    /**
     * Returns the MIME type of the current file
     *
     * @see \yii\helpers\BaseFileHelper
     */
    public function mime() : ?string
    {
        return FileHelper::getMimeType($this->real_path);
    }


    /**
     * Returns permissions of current filesystem object (UNIX systems)
     */
    public function permissions() : ?string
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        return substr(sprintf('%o', fileperms($this->real_path)), -4);
    }


    /**
     * Returns owner user of current filesystem object (UNIX systems)
     */
    public function owner() : mixed
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        $owner = fileowner($this->real_path);

        if ( is_int($owner) && function_exists('posix_getpwuid') )
        {
            $vec_owner = posix_getpwuid($owner);
            if ( !empty($vec_owner) && isset($vec_owner['name']) )
            {
                return $vec_owner['name'];
            }
        }

        return $owner;
    }


    /**
     * Returns owner group of current filesystem object (UNIX systems)
     */
    public function group() : mixed
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        $group = filegroup($this->real_path);

        if ( is_int($group) && function_exists('posix_getgrgid') )
        {
            $vec_group = posix_getgrgid($group);
            if ( !empty($vec_group) && isset($vec_group['name']) )
            {
                return $vec_group['name'];
            }
        }

        return $group;
    }


    /**
     * Returns the current file last modified date in UNIX format
     */
    public function updatedDate() : ?int
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

         return filemtime($this->real_path);
    }


    /**
     * Returns the current file last access date in UNIX format
     */
    public function lastAccessDate() : ?int
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

         return fileatime($this->real_path);
    }


    /**
     * Returns the current filesystem object contents
     */
    public function read(array $vec_options = []) : string|array|null
    {
        if ( $this->isReadable() )
        {
            // Get contents from a file object
            if ( $this->isFile() )
            {
                $contents = file_get_contents($this->real_path);
                if ( $contents )
                {
                    return $contents;
                }
            }

            // Get contents from a directory object
            return $this->readDirectory($vec_options);
        }

        return null;
    }


    /**
     *  List of the contents of the current directory
     *
     * @see \yii\helpers\BaseFileHelper
     */
    private function readDirectory(array $vec_options = []) : ?array
    {
        if ( ! $this->isDirectory() )
        {
            return null;
        }

        return FileHelper::findFiles($this->real_path, $vec_options);
    }


    /**
     * Return the size of current directory in bytes
     */
    private function directorySize(array $vec_options = [])
    {
        if ( ! $this->isDirectory() )
        {
            return 0;
        }

        $directory_size = 0;
        $vec_files = $this->read($vec_options);
        if ( !empty($vec_files) )
        {
            foreach ( $vec_files as $file )
            {
                if ( is_file($file) )
                {
                    $directory_size += sprintf("%u", filesize($file));
                }
            }
        }

        return $directory_size;
    }


    /**
     * Return a value from the file information array
     */
    private function getInfo($name)
    {
        // Load information array
        if ( $this->vec_info === null )
        {
            $this->info();
        }


        if ( array_key_exists($name, $this->vec_info) )
        {
            return $this->vec_info[$name];
        }

        return null;
    }
}
