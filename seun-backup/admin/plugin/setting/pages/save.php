<?php
include "../../../engine.autoloader.php";
$CONN = $engine->db();


if (empty($_REQUEST['name'])) {
    echo "<div style='color:red;'>Name is compulsory</div>";
    exit;
}


if (empty($_REQUEST['address']) || empty($_REQUEST['dob'])) {
    echo "<div style='color:red;'>All Fields are compulsory</div>";
    exit;
}


$username = $_REQUEST['username'];
$mobile = $_REQUEST['mobile'];
$adminid = $_REQUEST['adminid'];
$sex = $_REQUEST['sex'];
$role = $_REQUEST['role'];
$name = $_REQUEST['name'];
$address = $_REQUEST['address'];
$dob = $_REQUEST['dob'];
$country = $_REQUEST['country'];
$state = $_REQUEST['state'];
$lga = $_REQUEST['lga'];


$insert = $CONN->prepare("UPDATE admin SET name = ?, address = ?, mobile = ?, dob = ?, state = ?, role = ?, sex = ?, country = ?, lga = ? WHERE adminid = ? LIMIT 1");
$insert->execute(array(
    $name,
    $address,
    $mobile,
    $dob,
    $state,
    $role,
    $sex,
    $country,
    $lga,
    $adminid));


if ($engine->config("allow_user_theme_sellection")) {
    if (isset($_REQUEST['theme'])) {
        if (!empty($_REQUEST['theme'])) {
            $theme = $_REQUEST['theme'];
            $sitewidth = "100%";
            $allow_widgetstate = "false";
            $icon_size = "2.0";

            $xml = "../../../" . $engine->config("theme_folder") . $theme . "/parameter.xml";
            if (file_exists($xml)) {

                $xmlDoc = new DOMDocument();
                $xmlDoc->load($xml);

                $x = $xmlDoc->documentElement;
                foreach ($x->childNodes as $item) {
                    if (strlen($item->nodeValue) > 1) {
                        if ($item->tagName == "width") {
                            $sitewidth = trim($item->nodeValue);
                        }

                        if ($item->tagName == "dashbord_setting") {
                            $allow_widgetstate = trim($item->nodeValue);
                        }

                        if ($item->tagName == "icon_size") {
                            $icon_size = trim($item->nodeValue);
                        }


                    }
                }
                //hotel4/2.0/100%/false
                $_SESSION['theme'] = "$theme/$icon_size/$sitewidth/$allow_widgetstate";

            }


        }
    }

}


define('DESIRED_IMAGE_WIDTH', 50);
define('DESIRED_IMAGE_HEIGHT', 50);

if (!empty($_FILES["file"]["name"])) {


    $image = $_FILES["file"]["name"];
    function getExtension($str)
    {
        $boss = explode(".", strtolower($str));
        return end($boss);
    }

    $MAX_SIZE = 4000;

    $errors = 0;


    $uploadedfile = $_FILES['file']['tmp_name'];

    if ($image) {
        $extension = getExtension($image);
        $extension = strtolower($extension);
        if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "pjpeg") &&
            ($extension != "png") && ($extension != "gif")) {

            echo "<div style='color:red;'>$extension error-Wrong File Type</div>";
            exit;
            $errors = 1;
        } else {
            $size = filesize($_FILES['file']['tmp_name']);

            if ($size > $MAX_SIZE * 3024) {

                echo "<div style='color:red;'>error-Invalid File Size</div>";
                exit;
                $errors = 1;
            }

            if ($extension == "jpg" || $extension == "jpeg" || $extension == "pjpeg") {
                $uploadedfile = $_FILES['file']['tmp_name'];
                $src = imagecreatefromjpeg($uploadedfile);
            } else
                if ($extension == "png") {
                    $uploadedfile = $_FILES['file']['tmp_name'];
                    $src = imagecreatefrompng($uploadedfile);
                } else {
                    $src = imagecreatefromgif($uploadedfile);
                }

                list($width, $height) = getimagesize($uploadedfile);

            if ($width < 240) {
                $newwidth = $width;
            } else {
                $newwidth = 240;
            }


            $source_aspect_ratio = $width / $height;
            $desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;
            if ($source_aspect_ratio > $desired_aspect_ratio) {
                //
                // Triggered when source image is wider
                //
                $temp_height = DESIRED_IMAGE_HEIGHT;
                $temp_width = (int)(DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
            } else {
                //
                // Triggered otherwise (i.e. source image is similar or taller)
                //
                $temp_width = DESIRED_IMAGE_WIDTH;
                $temp_height = (int)(DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
            }


            $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
            imagealphablending($temp_gdim, false);
            imagesavealpha($temp_gdim, true);
            imagecopyresampled($temp_gdim, $src, 0, 0, 0, 0, $temp_width, $temp_height, $width,
                $height);


            $x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
            $y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;

            $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
            imagealphablending($desired_gdim, false);
            imagesavealpha($desired_gdim, true);
            imagecopy($desired_gdim, $temp_gdim, 0, 0, $x0, $y0, DESIRED_IMAGE_WIDTH,
                DESIRED_IMAGE_HEIGHT);


            $newheight = ($height / $width) * $newwidth;
            $tmp = imagecreatetruecolor($newwidth, $newheight);


            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);


            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


            $newfilename = $image;
            $useChars = 'AEUYBDGHJLMNPQRSTVWXZ123456789';
            $randdomnum = $useChars{mt_rand(0, 20)};
            for ($i = 1; $i < 20; $i++) {
                $randdomnum .= $useChars{mt_rand(0, 20)};
            }
            $rander = rand(000000000, 999999999);
            //$imagename = $randdomnum . $rander . "." . $extension;
            $filemainname = $username . ".jpg";

            $filename = "../../../avater/" . $filemainname;
            $filename1 = "../../../avater/small_" . $filemainname;
            //$filename3 ="../avater/small2_" . $filemainname;


            if ($extension == "jpg" || $extension == "jpeg" || $extension == "pjpeg") {
                imagejpeg($tmp, $filename, 95);
                imagejpeg($desired_gdim, $filename1, 90);
                //imagejpeg($desired_gdim3, $filename3, 90);
            } else
                if ($extension == "gif") {

                    imagegif($tmp, $filename, 95);
                    imagegif($desired_gdim, $filename1, 90);
                    //imagegif($desired_gdim3, $filename3, 90);
                } else
                    if ($extension == "png") {
                        imagepng($tmp, $filename, 9);
                        imagepng($desired_gdim, $filename1, 9);
                        //imagepng($desired_gdim3, $filename3, 9);
                    }


            imagedestroy($src);
            imagedestroy($tmp);
            imagedestroy($desired_gdim);

        }


    }
}


if (isset($_REQUEST['password']) && isset($_REQUEST['oldpassword'])) {
    if (!empty($_REQUEST['password'])) {

        if (!isset($_REQUEST['password2'])) {
            echo "<div style='color:red;'>Retype thesame password twice</div>";
            exit;
        }
        if ($_REQUEST['password2'] != $_REQUEST['password']) {
            echo "<div style='color:red;'>Retype thesame password twice</div>";
            exit;
        }

        if (empty($_REQUEST['oldpassword'])) {
            echo "<div style='color:red;'>Stored password does not match</div>";
            exit;
        }


        $presult = $CONN->prepare("SELECT password FROM admin WHERE adminid = ? LIMIT 1");
        $presult->execute(array($adminid));
        $prow = $presult->fetch(PDO::FETCH_ASSOC);
        $dbpaddword = $prow['password'];
        $password = sha1(md5($_REQUEST['oldpassword']) . $engine->config("user_key"));
        if ($password != $dbpaddword) {
            echo "<div style='color:red;'>Stored password does not match</div>";
            exit;
        }

        $npassword = sha1(md5($_REQUEST['password2']) . $engine->config("user_key"));
        $insert = $CONN->prepare("UPDATE admin SET password = ? WHERE adminid = ? LIMIT 1");//, plainpassword = ? $_REQUEST['password2'],
        $insert->execute(array(
            $npassword,
            $adminid));

        if (!$engine->config("local_authentication")) {
            $postData = array(
                "username" => $engine->get_session('adminme'),
                "password" => $_REQUEST['password'],
                "server_id" => $engine->config('server_id'));
            $return = $engine->file_get($postData, $engine->config('authentication_server') .
                'api/core/admin/change_password.json');
            $return = json_decode($return, true);
        }
    }
}


echo "ok";
exit;

?>