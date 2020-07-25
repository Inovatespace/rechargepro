<?php
class core extends Api
{

    public function __construct($method)
    {
        //Api::Api_Method("sync", "GET", $method);
        //Api::Api_Method("sync", "POST", $method);
    }

    public function status($parameter)
    {

        $notification = self::admin_notification();

        //$space = $diskfreespace."/".$totalspace;
        $array = array(
            "hard_disk" => $notification["usedspace"],
            "health" => "20",
            "admin" => $notification['adminusers'],
            "plugin" => $notification["plugin"],
            "widget" => $notification["widget"],
            "backup" => $notification["last_backup"]);

        return $array;

    }

    public function folderSize($dir)
    {
        $count_size = 0;
        $count = 0;
        $dir_array = scandir($dir);
        foreach ($dir_array as $key => $filename) {
            if ($filename != ".." && $filename != ".") {
                if (is_dir($dir . "/" . $filename)) {
                    $new_foldersize = self::foldersize($dir . "/" . $filename);
                    $count_size = $count_size + $new_foldersize;
                } else
                    if (is_file($dir . "/" . $filename)) {
                        $count_size = $count_size + filesize($dir . "/" . $filename);
                        $count++;
                    }
            }
        }
        return $count_size;
    }

    public function do_backup($parameter)
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '464M');
        if (isset($parameter["array"])) {
            $backup = $parameter["array"];
            $fileexplode = explode(",", $backup);

            foreach ($fileexplode as $tmpcource) {
                if (!empty($tmpcource)) {

                    if ($tmpcource != "backup_database") {
                        $location = self::backup_location(2);
                        $filename = "../tmp/$tmpcource.zip";

                        $source = "../" . $location[$tmpcource];
                        $dirlist = new RecursiveDirectoryIterator($source);
                        $filelist = new RecursiveIteratorIterator($dirlist);
                        // set script timeout value
                        // instantate object
                        $zip = new ZipArchive();
                        // create and open the archive
                        if ($zip->open("$filename", ZipArchive::CREATE) !== true) {
                            die("Could not open archive");
                        }
                        // add each file in the file list to the archive
                        foreach ($filelist as $key => $value) {
                            $number = strripos($key, "\\");
                            $remains = substr($key, $number, 3);
                            if ($remains != "\." && $remains != "\..") {
                                $newfile = str_ireplace("../", "", $key);
                                $zip->addFile($key, $newfile) or die("ERROR: Could not add file: $newfile");
                                //echo $key."-".$remains."<br />";
                            }

                        }

                        $zip->close();
                    } else {
                        $dumpSettings = array(
                            'compress' => Mysqldump::NONE, //
                            'no-data' => false,
                            'add-drop-table' => true,
                            'single-transaction' => true,
                            'lock-tables' => true,
                            'add-locks' => true,
                            'extended-insert' => false,
                            'disable-keys' => true,
                            'skip-triggers' => false,
                            'add-drop-trigger' => true,
                            'databases' => false,
                            'add-drop-database' => false,
                            'hex-blob' => true,
                            'no-create-info' => false,
                            'where' => '');

                        $dump = new Mysqldump($dumpSettings);

                        $dump->start("../tmp/backup_database.sql");
                        $zip = new ZipArchive();
                        // create and open the archive
                        if ($zip->open("../tmp/backup_database.zip", ZipArchive::CREATE) !== true) {
                            die("Could not open archive");
                        }
                        $zip->addFile("../tmp/backup_database.sql", "database.sql") or die("ERROR: Could not add file: $newfile");
                        $zip->close();
                        @unlink("../tmp/backup_database.sql");
                    }
                }
            }
        }
        return "done";
    }

    public function remove_backup($parameter)
    {
        $dell = $parameter['file'];
        @unlink("../tmp/" . $dell);
    }

    public function files_tobackup()
    {

        $tobackuparray = self::backup_location(2);
        $backuparray = self::backup_location(1);

        $tofilearray = array();
        foreach ($tobackuparray as $key => $value) {
            $file = "../" . $value;
            if (file_exists($file)) {
                $size = $size = self::folderSize($file);
                //$date = filemtime($file);
                if ($size > 0) {
                    $tofilearray[$key] = array("name" => $backuparray[$key], "size" => self::
                            byteconvert($size));
                }
            }
        }
        return $tofilearray;
    }


    public function files_backup()
    {
        $filearray = array();
        $backuparray = self::backup_location(1);
        foreach ($backuparray as $key => $value) {
            $file = "../tmp/" . $key . ".zip";
            if (file_exists($file)) {
                $size = filesize($file);
                $date = filemtime($file);
                $filearray[$key] = array(
                    "name" => $value,
                    "size" => self::byteconvert($size),
                    "date" => $date);
            }
        }
        return $filearray;
    }


    public function contains($str, array $arr)
    {
        foreach ($arr as $a) {
            if (stripos($str, $a) !== false)
                return true;
        }
        return false;
    }

    public function set_firewall($parameter)
    {

        $file = "../.htaccess";


        $fh = fopen($file, 'r');
        $data = fread($fh, (filesize($file) + 1000));
        fflush($fh);
        fclose($fh);

        /* PLUGINS START */

        $config = $parameter['ip'];


        //$pattern = "/^#deny start(.+)#deny end $/";
        $replacement = '
#deny start 
' . $config . '
#deny end
';
        //$data = preg_replace($pattern, $replacement, $data);

        //$data = preg_replace('/'.preg_quote('#deny start').'.*?'.preg_quote('#deny end').'/', $replacement, $data);

        $data = preg_replace('@\#deny start[^\]]+\#deny end@', $replacement, $data);


        if (is_writable($file)) {
            if (!$handle = fopen($file, 'w')) {
                echo "Cannot open file ($file)";
                exit;
            }

            if (fwrite($handle, $data) === false) {
                echo "Cannot write to file ($file)";
                exit;
            }
            fflush($handle);
            fclose($handle);

        } else {
            echo "The file $file is not writable. Please CHMOD config.php to 777.";
            exit;
        }


    }


    public function get_firewall()
    {
        $file = "../.htaccess";


        $myfile = array();
        $fh = fopen($file, 'rb');
        $start = false;
        $end = false;
        $return = 1;
        while ($line = fgets($fh, 1000)) {

            if (strpos($line, "order allow,deny") !== false) {
                $return = 1;
            }

            if (strpos($line, "order deny,allow") !== false) {
                $return = 2;
            }


            if (strpos($line, "#deny end") !== false) {
                $start = false;
                break;
            }

            if ($start) {
                $line = trim($line);
                if (self::contains($line, array("allow from", "deny from"))) {
                    $to = preg_split('/(allow from|deny from)/', $line);
                    $result = preg_replace("/[^a-zA-Z0-9.-]+/", "", $to[1]);
                    if (filter_var($result, FILTER_VALIDATE_IP)) {
                        $myfile[] = $result;
                    } else {

                        $explode = explode(".", $result);
                        $count = count($explode);
                        if ($count > 2) {
                            if (is_numeric($explode[0]) && is_numeric($explode[1]) && is_numeric($explode[2])) {
                                if ($explode[$count - 1] == "") {
                                    $myfile[] = $result;
                                }
                                if (strpos($explode[$count - 1], "-")) {
                                    $myfile[] = $result;
                                }
                            }
                        }

                    }
                }
                continue;
            }

            if (strpos($line, "#deny start") !== false) {
                $start = true;
            }


        }


        fflush($fh);
        fclose($fh);

        //1 = deny
        //2 = allow onli ip
        return array($return, implode("&#13;", $myfile));

    }
    
    public function serpermission(){
        $filemode = 0755;
        $chmodarray = self::backup_location(2);
        foreach($chmodarray AS $folder){
         if(file_exists("../".$folder)){ 
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../".$folder));
         foreach($iterator as $item) {
            chmod($item, $filemode);
        }
        }
        }
        
        chmod("../config/config.php", $filemode);
        chmod("../tmp", $filemode);
        chmod("../cache", $filemode);
        chmod("../log", $filemode);
        chmod("../stalk/cache", $filemode);
        chmod("../.htaccess", $filemode);
        //config,config all download folder,tmp,cach,log
        //chat cach
        //chmod("/somedir/somefile", 0755);
        
        //$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathname));
        //foreach($iterator as $item) {
          //  chmod($item, $filemode);
        //}
        return "";
        
    }
    
    private function get_server_key(){
        return self::config("user_key");
    }
    
    public function server_reimage($parameter){
        
         $return = "Site Key not valid for this package";
         
        if($parameter['sitekey'] == self::get_server_key()){
            echo "ok";
            self::generate_format_server();
            //$return = "ok";
        }
        
        
        return $return;
    }
    
private function generate_format_server(){

$filetowrite =  '<?php
ini_set("max_execution_time", 5000);
ini_set("memory_limit", "464M");

$dir = ".";
rrmdir($dir);

$address = "'.self::config("server_address").'";
$targetFile = fopen( "install.zip", "w");
$licence = "licence='.self::config("licence").'";
$ch = curl_init( $address."/core_main/reimage.php" );
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_INTERFACE, "127.0.0.1");
curl_setopt($ch, CURLOPT_REFERER, "http://safeparkingltd.com");
curl_setopt($ch, CURLOPT_USERAGENT, "Firefox (WindowsXP) – Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt($ch, CURLOPT_POSTFIELDS, $licence);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
curl_setopt( $ch, CURLOPT_FILE, $targetFile );
curl_exec( $ch );
fclose( $targetFile );

unzip_plugin("install.zip", "./");



function unzip_plugin($ziplocation, $plugindirectory)
{
    $zip = new ZipArchive;
    $continue = 0;
    if ($zip->open($ziplocation))
    {
        for ($i = 0; $i < $zip->numFiles; $i++)
        {
            $zip->getNameIndex($i);
            $continue = 1;
        }
    }

    if ($continue == 1)
    {
        $res = $zip->open($ziplocation);
        if ($res === true)
        {
            $zip->extractTo($plugindirectory);
            $zip->close();
            //update db
        }

    }

    @unlink($ziplocation);
  

}





function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir"){ 
           rrmdir($dir."/".$object); }
        else {
            if($object != "config.php"){
            unlink($dir."/".$object);
            }
            }
      }
    }
    reset($objects);
    if($dir != "."){
    if(@!rmdir($dir)){
        chmod($dir, 0777);
      if(@!rmdir($dir)){
        chmod($dir, 0777);
        recursiveRemove($dir);
        }
    };
    }
  }
 }
 
function recursiveRemove($dir) {
    $structure = glob(rtrim($dir, "/")."/*");
    if (is_array($structure)) {
        foreach($structure as $file) {
            if (is_dir($file)){ recursiveRemove($file);}
            elseif (is_file($file)) {
                 if($file != "./config.php"){
                unlink($file);
                }
                }
        }
    }
    if($dir != "."){
        if(@!rmdir($dir)){
        chmod($dir, 0777);
      if(@!rmdir($dir)){
        chmod($dir, 0777);
        rmdir_files($dir);
        }
        }
    };
}

function rmdir_files($dir) {
 $dh = opendir($dir);
 if ($dh) {
  while($file = readdir($dh)) {
   if (!in_array($file, array(".", ".."))) {
    if (is_file($dir.$file)) {
     unlink($dir.$file);
    }
    else if (is_dir($dir.$file)) {
     rmdir_files($dir.$file);
    }
   }
  }
  rmdir($dir);
 }
}
?>
';

$myFile = "../config.php";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $filetowrite);
fclose($fh);

#$ch = curl_init( self::config("website_root")."/config.php" );
#curl_setopt($ch, CURLOPT_POST, true);
#curl_setopt($ch, CURLOPT_INTERFACE, "127.0.0.1");
#curl_setopt($ch, CURLOPT_REFERER, "http://safeparkingltd.com");
#curl_setopt($ch, CURLOPT_USERAGENT, "Firefox (WindowsXP) – Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
#curl_setopt($ch, CURLOPT_POSTFIELDS, $licence);
#curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
#curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
#curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
#curl_setopt( $ch, CURLOPT_FILE, $targetFile );
#curl_setopt($ch, CURLOPT_TIMEOUT, 10);
#curl_exec( $ch );
#

    }
    
    
    
    public function update(){
        //java,css, theme available, cores , all files from home
    }


}
?>