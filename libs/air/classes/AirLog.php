<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Air log
 */

class AirLog
{
    private $fp = false;
    private $filename;

    /**
     * AirLog constructor.
     * @param $filename string path to file
     * @throws Exception
     */
    function __construct($filename)
    {
        $this->filename = $filename;
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                throw new Exception("Failed to create directory {$dir}");
            }
        }

        $this->fp = @fopen($this->filename, 'a');
        if ($this->fp === false) {
            throw new Exception("Failed to open/create file for writing {$this->filename}");
        }
    }

    /** Write line to log
     * @param $message
     * @throws Exception
     */
    function writeLn($message)
    {
        if (false === @fwrite($this->fp, sprintf("%s: %s\n", date('Y-m-d H:i:s'), $message))) {
            throw new Exception("Failed write to file {$this->filename}");
        }
    }

    /**
     * Close file pointer
     */
    function close()
    {
        if ($this->fp !== false) {
            fclose($this->fp);
            $this->fp = false;
        }
    }

    function __destruct()
    {
        $this->close();
    }
}