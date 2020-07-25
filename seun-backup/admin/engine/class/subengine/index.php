<?php


function index(){
	$mgbig = self::config("theme_folder") . self::config("theme") .
                "/images/default.png";
            $mgsmall = self::config("theme_folder") . self::config("theme") .
                "/images/small_default.png";
            if (file_exists("avater/" . self::get_session('adminme') . ".jpg")) {
                $mgbig = "avater/" . self::get_session('adminme') . ".jpg";
                $mgsmall = "avater/small_" . self::get_session('adminme') . ".jpg";
            }

            $breadcrumb = "";
            if (isset($_REQUEST['p'])) {

                $breadcrumb = self::safe_html($_REQUEST['p']);
                if ($_REQUEST['p'] == "index" || empty($_REQUEST['p'])) {
                    $breadcrumb = self::safe_html($_REQUEST['u']);
                }

            } else {
                if (!isset($_REQUEST['u']) || empty($breadcrumb)) {
                    $breadcrumb = "dashboard";
                } else {
                    $breadcrumb = self::safe_html($_REQUEST['u']);
                }
            }


            $plugindetails = self::xml_details();
            $notification = self::site_notification();
            $submenu = self::sub_menu();
            return array(
                "{RIGHT_WIDGET}" => self::right_widget(),
                "{LEFT_WIDGET}" => self::left_widget(),
                "{BODY_HOLDER}" => self::config("body_holder"),
                "{PLUGIN_MENU}" => $submenu[1],
                "{BODY}" => self::body(),
                "{BODY_SMALL}" => self::body_small(),
                "{TOP_MENU}" => self::top_menu(),
                "{SITE_MENU}" => self::site_menu(),
                "{SITE_LOGO}" => self::config("theme_folder") . self::config("theme") .
                    "/images/logo.png",
                "{SITE_LOCATION}" => self::config("theme_folder") . self::config("theme"),
                "{SITE_NAME}" => self::config("app_name"),
                "{LOGIN_NAME}" => self::get_session('name'),
                "{AVATER_SMALL}" => $mgsmall,
                "{AVATER_BIG}" => $mgbig,
                "{FOOTER}" => $footer,
                "{LOGOUT}" => '<div id="topmenu"><div class="topmenucontent"><ul><li id="top_logout"><a style="cursor: pointer;" href="logout"><span></span>logout</a></li></ul></div></div>',
                "{BREAD_CRUMB}" => $breadcrumb,
                "{LANGUAGE}" => $_COOKIE['language'],
                "{LANGUAGE_FILE}" => self::site_language(),
                "{NOTIFICATION_MEMO}" => $notification[0],
                "{NOTIFICATION_MEMO_COUNT}" => $notification[3],
                "{NOTIFICATION_TASK}" => $notification[1],
                "{NOTIFICATION_TASK_COUNT}" => $notification[4],
                "{NOTIFICATION_EMAIL}" => $notification[2],
                "{NOTIFICATION_EMAIL_COUNT}" => $notification[5],
                "{PLUGIN_KEY}" => $plugindetails['key'],
                "{PLUGIN_KEY_MENU_COUNT}" => $submenu[0],
                "{PLUGIN_NAME}" => $plugindetails['oruko'],
                "{PLUGIN_DETAILS}" => $plugindetails['details'],
                "{PLUGIN_IMAGE_CLASS}" => $plugindetails['image_class'],
               );
               
               }
?>