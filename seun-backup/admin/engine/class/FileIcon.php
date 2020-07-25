<?php

class FileIcon
{
    var $filename;
    var $size;
    var $iconurl = 'engine/class/icon/elegant/';

    var $icons = array(

        // Microsoft Office
        'doc' => array('document', 'Word Document'),
        'docx' => array('document', 'Word Document'),
        'xls' => array('document', 'Excel Spreadsheet'),
        'xlsx' => array('document', 'Excel Spreadsheet'),
        'ppt' => array('document', 'PowerPoint Presentation'),
        'pptx' => array('document', 'PowerPoint Presentation'),
        'pps' => array('document', 'PowerPoint Presentation'),
        'pot' => array('document', 'PowerPoint Presentation'),
        'rtf' => array('document', 'RTF File'),

        'mdb' => array('access', 'Access Database'),
        'vsd' => array('visio', 'Visio Document'),
        //	'xxxx' => array('project', 'Project Document'), 	// dont remember type...


        // XML
        'htm' => array('htm', 'HTML Document'),
        'html' => array('htm', 'HTML Document'),
        'xml' => array('xml', 'XML Document'),

        // Images
        'jpg' => array('image', 'JPEG Image'),
        'jpe' => array('image', 'JPEG Image'),
        'jpeg' => array('image', 'JPEG Image'),
        'gif' => array('image', 'GIF Image'),
        'bmp' => array('image', 'Windows Bitmap Image'),
        'png' => array('image', 'PNG Image'),
        'tif' => array('image', 'TIFF Image'),
        'tiff' => array('image', 'TIFF Image'),

        // Audio
        'mp3' => array('audio', 'MP3 Audio'),
        'wma' => array('audio', 'WMA Audio'),
        'mid' => array('audio', 'MIDI Sequence'),
        'midi' => array('audio', 'MIDI Sequence'),
        'rmi' => array('audio', 'MIDI Sequence'),
        'au' => array('audio', 'AU Sound'),
        'snd' => array('audio', 'AU Sound'),

        // Video
        'mpeg' => array('video', 'MPEG Video'),
        'mpg' => array('video', 'MPEG Video'),
        'mpe' => array('video', 'MPEG Video'),
        'wmv' => array('video', 'Windows Media File'),
        'avi' => array('video', 'AVI Video'),
        'mp4' => array('video', 'MP4 Video'),

        // Archives
        'zip' => array('zip', 'ZIP Archive'),
        'rar' => array('zip', 'RAR Archive'),
        'cab' => array('zip', 'CAB Archive'),
        'gz' => array('zip', 'GZIP Archive'),
        'tar' => array('zip', 'TAR Archive'),
        'zip' => array('zip', 'ZIP Archive'),
        'jar' => array('zip', 'ZIP Archive'),
        'java' => array('zip', 'ZIP Archive'),
        'apk' => array('zip', 'ZIP Archive'),

        // OpenOffice
        'sdw' => array('oo-write', 'OpenOffice Writer document'),
        'sda' => array('oo-draw', 'OpenOffice Draw document'),
        'sdc' => array('oo-calc', 'OpenOffice Calc spreadsheet'),
        'sdd' => array('oo-impress', 'OpenOffice Impress presentation'),
        'sdp' => array('oo-impress', 'OpenOffice Impress presentation'),

        // Others
        'txt' => array('txt', 'Text Document'),
        'sql' => array('sql', 'Text Document'),
        'js' => array('js', 'Javascript Document'),
        'dll' => array('binary', 'Binary File'),
        'exe' => array('binary', 'Binary File'),
        'pdf' => array('pdf', 'Adobe Acrobat Document'),
        'php' => array('php', 'PHP Script'),
        'ps' => array('ps', 'Postscript File'),
        'dvi' => array('dvi', 'DVI File'),
        'swf' => array('swf', 'Flash'),
        'chm' => array('chm', 'Compiled HTML Help'),


        'psd' => array('psd', 'Photoshop'),


        // Unkown
        'default' => array('_blank', 'Unkown Document'),
        );


    /**
     * Constructor of class
     * @param string $filename filename
     * @desc Constructor of class
     */
    function FileIcon($filename)
    {
        $this->filename = $filename;
        //$this -> size = filesize($this -> filename);
    }

    /**
     * Set the url for icons
     * @param string $url url icon
     * @desc Set the url for icons
     */
    function setIconUrl($iconurl)
    {
        $this->iconurl = $iconurl;
    }


    function downloadLink()
    {
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');


        header('Pragma: no-cache');
        header('Cache-Control: no-cache, no-store, must-revalidate');


        if ($fd = fopen($this->filename, "r"))
        {
            $fsize = filesize($this->filename);
            $path_parts = pathinfo($this->filename);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext)
            {
                case "zip":
                    header("Content-type: application/zip"); // add here more headers for diff. extensions
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\""); // use 'attachment' to force a download
                    break;

                case "mp3":
                    header("Content-type: application/mp3");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\"");
                    break;

                case "pdf":
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\"");
                    break;

                case "doc":
                    header("Content-type: application/msword");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\"");
                    break;

                case "xls":
                    header("Content-type: application/vnd.ms-excel");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\"");
                    break;

                case "ppt":
                    header("Content-type: application/vnd.ms-powerpoint");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\"");
                    break;

                case "js":
                    header("Content-type: text/javascript");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] .
                        "\"");
                    break;

                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
            }


            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd))
            {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);
    }


    function foldersize()
    {
        $total_size = 0;
        $files = scandir($this->filename);
        $cleanPath = rtrim($this->filename, '/') . '/';

        foreach ($files as $t)
        {
            if ($t <> "." && $t <> "..")
            {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile))
                {
                    $size = foldersize($currentFile);
                    $total_size += $size;
                } else
                {
                    $size = filesize($currentFile);
                    $total_size += $size;
                }
            }
        }

        return $total_size;
    }

    /**
     * Returns file size
     * @return string
     * @desc Returns file size
     */
    function getfileSize()
    {
        return filesize($this->filename);
    }


    /**
     * Returns the timestamp of the last change
     * @return timestamp $timestamp The time of the last change as timestamp
     * @desc Returns the timestamp of the last change
     */
    function modifiedTime()
    {
        return date("Y-m-d H:i:s", strtotime("+0 days", filemtime($this->filename)));
    }

    /**
     * Returns the filename
     * @return string $filename The filename
     * @desc Returns the filename
     */
    function getName()
    {
        return $this->filename;
    }

    /**
     * Returns user id of the file
     * @return string $user_id The user id of the file
     * @desc Returns user id of the file
     */
    function getOwner()
    {
        return fileowner($this->filename);
    }

    /**
     * Returns group id of the file
     * @return string $group_id The group id of the file
     * @desc Returns group id of the file
     */
    function getGroup()
    {
        return filegroup($this->filename);
    }

    /**
     * Returns the suffix of the file
     * @return string $suffix The suffix of the file. If no suffix exists FALSE will be returned
     * @desc Returns the suffix of the file
     */
    function getType()
    {
        $file_array = split("\.", $this->filename); // Splitting prefix and suffix of real filename
        $suffix = $file_array[count($file_array) - 1]; // Returning file type
        if (strlen($suffix) > 0)
        {
            return $suffix;
        } else
        {
            return false;
        }
    }
    
    function getFiletype(){
          $extension = $this->getType();
        if (key_exists($extension, $this->icons))
            return $this->icons[$extension][0];
        else
            return $this->icons['default'][0];
    }

    /**
     * Returns the size of the file
     * @param int $size
     * @return int
     * @desc Returns the size of the file
     */
    function evalSize($size)
    {
        if ($size >= 1073741824)
            return round($size / 1073741824 * 100) / 100 . " GB";
        elseif ($size >= 1048576)
            return round($size / 1048576 * 100) / 100 . " MB";
        elseif ($size >= 1024)
            return round($size / 1024 * 100) / 100 . " KB";
        else
            return $size . " BYTE";
    }
    
    
    function getExtension(){
        $boss = explode(".",strtolower($this->filename));
        return end($boss);
    }

    /**
     * Returns the icon
     * @desc Returns the icon of the file
     */
    function getIcon()
    {
        $extension = strtolower($this->getType());
        if (key_exists($extension, $this->icons))
            return array_merge($this->icons[$extension], array($extension));
        else
            return array_merge($this->icons['default'], $this->icons['default']);
    }

    /**
     * Display Icon
     * @return string $size
     * @desc Display Icon
     */
    function displayIcon()
    {
        $array = $this->getIcon();
        return $this->iconurl . strtolower($array[2]) . '.png';
    }

}

?>