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
use dezero\errors\ShellCommandException;
use mikehaertl\shellcommand\Command as ShellCommand;
use Yii;

/**
 * File is the base class to handle filesystem objects
 */
class File extends \yii\base\BaseObject
{
    /**
     * @var Default permissions
     */
    const DEFAULT_PERMISSION  = 775;


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
     * @var resource|null holds the file handler resource if the file is opened
     */
    private $handle;


    /**
     * File constructor
     */
    public function __construct(string $file_path, array $vec_config = [])
    {
        $this->file_path = $file_path;
        $this->real_path = FileHelper::realPath($file_path);

        // Clear PHP's internal stat cache
        clearstatcache();

        // Load path information
        if ( $this->exists() )
        {
            $this->info();
        }

        // BaseObject::construct() must be called
        parent::__construct($vec_config);
    }


    /**
     * Closes the current file if it is opened
     */
    public function __destruct()
    {
        $this->close();
    }


    /**
     * Load a filesystem object given a path
     *
     * @return static
     */
    public static function load(string $file_path) : self
    {
        return new static($file_path);
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


    /*
    |--------------------------------------------------------------------------
    | GETTER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if current filesystem object exists
     */
    public function exists() : bool
    {
        return file_exists($this->real_path);
    }

    /**
     * Return file path for the current filesystem object
     */
    public function filePath() : ?string
    {
        return $this->file_path;
    }


    /**
     * Return real path for the current filesystem object
     */
    public function realPath() : ?string
    {
        return $this->real_path;
    }


    /**
     * Alias for realPath() method
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
     * Alias for isDirectory() method
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
     * @see yii\helpers\BaseFileHelper::getMimeType()
     */
    public function mime() : ?string
    {
        return FileHelper::getMimeType($this->real_path);
    }


    /**
     * Returns permissions of current filesystem object (UNIX systems)
     *
     * @return string|int|null
     */
    public function permissions()
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        return substr(sprintf('%o', fileperms($this->real_path)), -4);
    }


    /**
     * Returns owner user of current filesystem object (UNIX systems)
     *
     * @return mixed
     */
    public function owner()
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
     *
     * @return mixed
     */
    public function group()
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
     * Returns the data of the current filesystem object
     *
     * @return string|array|null
     */
    public function read(array $vec_options = [])
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        // Get contents from a FILE
        if ( $this->isFile() )
        {
            $data = file_get_contents($this->real_path);
            if ( $data === false )
            {
                return null;
            }

            return $data;
        }

        // Get contents from a DIRECTORY
        return $this->readDirectory($vec_options);
    }


    /**
     * Check if current filesystem object is empty
     *
     * @see dezero\helpers\FileHelper::isEmptyDirectory()
     */
    public function isEmpty() : ?bool
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        // Check if a FILE is empty
        if ( $this->isFile() )
        {
            $data = file_get_contents($this->real_path);
            return $data === '';
        }

        // Check if a DIRECTORY is empty
        return FileHelper::isEmptyDirectory($this->real_path);
    }


    /**
     * Check if a file is an image
     *
     * @see dezero\helpers\FileHelper::isImage()
     */
    public function isImage() : bool
    {
        if ( ! $this->exists() || ! $this->isReadable() || ! $this->isFile() )
        {
            return false;
        }

        return FileHelper::isImage($this->real_path);
    }


    /**
     * Get MD5 Checksum of file with previous check of `filesize`
     *
     * @see https://www.php.net/manual/en/function.md5-file.php
     * @param int|bool $max_size
     * @return string|false
     */
    public function md5($max_size = 5)
    {
        if ( ! $this->exists() || ! $this->isReadable() || ! $this->isFile() )
        {
            return false;
        }

        if ( $max_size === true )
        {
            return md5_file($this->real_path);
        }

        $size = $this->size();
        if ( $size && $size < ($max_size * 1024) * 1024 )
        {
            return md5_file($this->real_path);
        }

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | SETTER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Sets the current filesystem object owner for UNIX systems.
     */
    public function setOwner(string $owner) : bool
    {
        if ( ! $this->exists() || ! chown($this->real_path, $owner) )
        {
            return false;
        }

        return true;
    }


    /**
     * Sets the current filesystem object group for UNIX systems.
     */
    public function setGroup(string $group) : bool
    {
        if ( ! $this->exists() || ! chgrp($this->real_path, $group))
        {
            return false;
        }

        return true;
    }


    /**
     * Sets the current filesystem object permissions for UNIX systems.
     */
    public function setPermissions(int $permissions) : bool
    {
        if ( ! $this->exists() || ! is_numeric($permissions) )
        {
            return false;
        }
        // '755' normalize to octal '0755'
        $permissions = octdec(str_pad($permissions, 4, "0", STR_PAD_LEFT));

        if ( ! @chmod($this->real_path, $permissions) )
        {
            return false;
        }

        return true;
    }


    /**
     * Alias for setPermissions() method
     */
    public function chmod(int $permissions) : bool
    {
        return $this->setPermissions($permissions);
    }


    /**
     * Creates empty file if the current file doesn't exist
     */
    public function createEmptyFile() : bool
    {
        // File cannot exist
        if ( $this->exists() )
        {
            return false;
        }

        if ( ! $this->open('w') )
        {
            return false;
        }

        touch($this->real_path);
        $this->close();

        $empty_file = self::load($this->real_path);
        $this->file_path = $empty_file->file_path;
        $this->real_path = $empty_file->real_path;
        $this->vec_info = $empty_file->vec_info;

        return true;
    }


    /**
     * Write data to the current filesystem object
     */
    public function write(string $data, bool $is_auto_create = true, int $flags = 0) : bool
    {
        if ( ! $this->exists() && $is_auto_create )
        {
            $this->createEmptyFile();
        }

        if ( ! $this->exists() || ! $this->isFile() || ! $this->isWritable() )
        {
            return false;
        }

        if ( ! file_put_contents($this->real_path, $data, $flags) )
        {
            return ( $data === '' );
        }

        return true;
    }


    /**
     * Append data to the current filesystem object
     */
    public function append(string $data) : bool
    {
        return $this->write($data, false, FILE_APPEND);
    }


    /**
     * Alias for append() method
     */
    public function save(string $data, bool $is_auto_create = true, int $flags = 0) : bool
    {
        return $this->append($data, $is_auto_create, $flags);
    }


    /**
     * Deletes the current filesystem object
     *
     * @see FileHelper::unlink()
     */
    public function delete() : bool
    {
        if ( ! $this->exists() || ! $this->isWritable() )
        {
            return false;
        }

        // Delete a FILE
        if ( $this->isFile() )
        {
            return FileHelper::unlink($this->real_path);
        }

        // Removes a directory
        return $this->removeDirectory();
    }



    /**
     * Copy the current filesystem object to specified destination
     */
    public function copy($destination_path, $is_overwrite = true) : ?self
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        $destination_path = $this->resolveDestinationPath($destination_path);
        $destination_file = self::load($destination_path);
        if ( $destination_file->exists() && ! $is_overwrite )
        {
            return null;
        }

        // Copy a FILE
        if ( $this->isFile() )
        {
            if ( ! @copy($this->real_path, $destination_path) )
            {
                return null;
            }

            // Replace information with new copied file
            return self::load($destination_path);
        }

        // Copies a directory
        return $this->copyDirectory($destination_path);
    }


    /**
     * Renames/moves the current filesystem object to specified destination
     */
    public function move($destination_path, $is_overwrite = true) : ?self
    {
        if ( ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        $destination_path = $this->resolveDestinationPath($destination_path);
        $destination_file = self::load($destination_path);
        if ( $destination_file->exists() && ! $is_overwrite )
        {
            return null;
        }

        // Rename a FILE or DIRECTORY
        if ( ! @rename($this->real_path, $destination_path) )
        {
            return null;
        }

        // Replace information with new copied file
        $moved_file = self::load($destination_path);
        $this->file_path = $moved_file->file_path;
        $this->real_path = $moved_file->real_path;
        $this->vec_info = $moved_file->vec_info;

        return $this;
    }


    /**
     * Alias for move() method
     */
    public function rename($destination_path, $is_overwrite = true) : ?self
    {
        return $this->move($destination_path, $is_overwrite);
    }


    /**
     * Clear the data of a filesystem object
     *
     * @see dezero\helpers\FileHelper::clearDirectory()
     */
    public function clear() : bool
    {
        if ( ! $this->exists() || ! $this->isWritable() )
        {
            return false;
        }

        // Delete a FILE
        if ( $this->isFile() )
        {
            return $this->write('');
        }

        // Removes all of a directory’s contents recursively.
        return $this->clearDirectory();
    }


    /**
     * Download the current file
     */
    public function download($file_name = null)
    {
        if ( $this->isDirectory() || ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        $file_name = $file_name === null ? $this->basename() : $file_name;

        return Yii::$app->response->sendFile($this->real_path, $file_name);
    }


    /**
     * Display the current file (download with content disposition inline)
     */
    public function display($file_name = null)
    {
        if ( $this->isDirectory() || ! $this->exists() || ! $this->isReadable() )
        {
            return null;
        }

        $file_name = $file_name === null ? $this->basename() : $file_name;

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->add('Content-Type', $this->mime());
        $response->headers->add('Content-Disposition', 'inline; filename="'.$file_name.'"');
        return $response->sendFile($this->real_path);
    }


    /**
     * ZIP current file via SHELL command
     */
    public function zip(bool $is_after_delete = false) : ?self
    {
        if ( ! $this->isFile() )
        {
            return null;
        }

        // Zip command
        $zip_command = 'zip';
        if ( isset(Yii::$app->params['zip_command']) )
        {
            $zip_command = Yii::$app->params['zip_command'];
        }

        // ZIP file destination path
        $zip_destination_path = $this->realPath() .'.zip';

        $shellCommand = new ShellCommand();
        $shellCommand->setCommand($zip_command .' -j '.  $zip_destination_path .' '. $this->realPath());

        // If we don't have proc_open, maybe we've got exec
        if ( ! function_exists('proc_open') && function_exists('exec') )
        {
            $shellCommand->useExec = true;
        }

        // Execute command
        $is_success = $shellCommand->execute();

        if ( ! $is_success )
        {
            $execCommand = $shellCommand->getExecCommand();
            throw new ShellCommandException($execCommand, $shellCommand->getExitCode(), $shellCommand->getStdErr());
        }
        else if ( $is_after_delete )
        {
            // Delete current file
            $this->delete();
        }

        return self::load($zip_destination_path);
    }


    /**
     * Resolves destination path for the current filesystem object.
     * This method enables short calls for {@link copy} & {@link rename} methods
     * (i.e. copy('document.pdf') makes a copy of the current filesystem object
     * in the same directory, named 'document.pdf')
     */
    public function resolveDestinationPath(string $destination_path) : string
    {
        // Check if it's the current directory
        if ( strpos($destination_path, DIRECTORY_SEPARATOR) === false)
        {
            return $this->dirname() . DIRECTORY_SEPARATOR . $destination_path;
        }

        return FileHelper::realPath($destination_path);
    }


    /**
     * Opens (if not already opened) the current file using certain mode
     *
     * Used only internally
     */
    private function open(string $mode = 'r')
    {
        if ( is_resource($this->handle) )
        {
            return true;
        }

        $this->handle = fopen($this->real_path, $mode);

        return is_resource($this->handle);
    }


    /**
     * Closes (if opened) the current file pointer
     *
     * Used only internally
     */
    private function close() : bool
    {
        if ( ! is_resource($this->handle) )
        {
            return true;
        }

        return fclose($this->handle);
    }


    /**
     * Return a value from the file information array
     */
    private function getInfo(string $name)
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



    /*
    |--------------------------------------------------------------------------
    | DIRECTORY METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Ensures directory exists and has specific permissions.
     *
     * If directory does not exists, create it!
     */
    public static function ensureDirectory(string $path, int $permissions = 775) : ?self
    {
        $directory = static::load($path);

        // Directory exists
        if ( $directory->exists() && $directory->isDir() )
        {
            return $directory;
        }

        return static::createDirectory($path, $permissions);
    }


    /**
     * Creates a new directory
     *
     * @see dezero\helpers\FileHelper::createDirectory
     */
    public static function createDirectory(string $path, int $permissions = 775) : ?self
    {
        // <DEZERO> - '755' normalize to octal '0755'
        $permissions = octdec(str_pad($permissions, 4, "0", STR_PAD_LEFT));

        if ( FileHelper::createDirectory($path, $permissions) )
        {
            return static::load($path);
        }

        return null;
    }


    /**
     * List of the contents of the current directory
     *
     * @see yii\helpers\BaseFileHelper::findFiles()
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
     * Removes a directory (and all its content) recursively.
     *
     * @see yii\helpers\BaseFileHelper::removeDirectory()
     */
    private function removeDirectory() : bool
    {
        if ( ! $this->isDirectory() )
        {
            return false;
        }

        FileHelper::removeDirectory($this->real_path);

        return ! $this->exists();
    }


    /**
     * Copies a whole directory as another one
     *
     * @see yii\helpers\BaseFileHelper::copyDirectory()
     */
    private function copyDirectory(string $destination_path) : ?self
    {
        if ( ! $this->isDirectory() )
        {
            return null;
        }

        FileHelper::copyDirectory($this->real_path, $destination_path);

        // Replace information with new copied directory
        return self::load($destination_path);
    }


    /**
     * Removes all of a directory’s contents recursively.
     *
     * @see dezero\helpers\FileHelper::clearDirectory()
     */
    private function clearDirectory() : bool
    {
        if ( ! $this->isDirectory() )
        {
            return false;
        }

        FileHelper::clearDirectory($this->real_path);

        return true;
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
     * Encodes the file content with MIME base64
     *
     * @see base64_encode()
     */
    public function base64_encode() : ?string
    {
        if ( ! $this->isFile() )
        {
            return null;
        }

        return base64_encode($this->read());
    }


    /**
     * Writes contents encoded with MIME base64 into the current file
     *
     * @see write()
     * @see base64_decode()
     */
    public function base64_save(string $encoded_data, bool $is_auto_create = true, int $flags = 0) : bool
    {
        if ( ! $this->isFile() )
        {
            return false;
        }

        return $this->write(base64_decode($encoded_data, $is_auto_create, $flags));
    }
}
