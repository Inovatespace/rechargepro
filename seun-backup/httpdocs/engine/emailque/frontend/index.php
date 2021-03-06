<?php

	/*
		EMailqueue
		Frontend component
	*/
	
	define("LIB_DIR", "lib");
	define("APP_DIR", "../");
	
	include APP_DIR."common.inc.php";

	
	$a = $utils->getglobal("a");
	if (!$a || $a == "")
		$a = "home";
	
	switch ($a) {
		case "home":
			include APP_DIR."classes/home.class.php";
			$home = new home();
			$out->add($home->getinfo());
			break;
		
		case "manager":
            include APP_DIR."classes/manager.class.php";
            $manager = new manager();
            $out->add($manager->run());
            break;

		case "servicetools":
			include APP_DIR."classes/servicetools.class.php";
			$servicetools = new servicetools();
			$out->add($servicetools->run());
			break;
	}
	
	// Control cases wich don't need head nor footer
	if ($utils->getglobal("aa") != "view_iframe_body") {
	   $out->add_tobeggining($html->head());
	   $out->add($html->foot());
	}
	
	$out->dump();
	
	$db->disconnect();

?>
