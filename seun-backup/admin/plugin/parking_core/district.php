<?php
switch ($street){
case "ASA":
case "Casandra Street":
case "Salt Lake":
case "Usuman Street":
case "Zambezi":
$district = "MAITAMA";
break;


case "Algiers":
case "Maputo":
case "Gwani":
case "Abijan":
case "Accra":
case "Algeria":
case "Cap Town Street":
case "Conakry":
case "Constatine":
case "Dalaba":
case "Doula Street":
case "Idimba":
case "Addis Ababa Crescent":
case "Nodola":
case "Sokode crescent":
$district = "WUSE I";
break;



case "Julius Nyerere Crescent":
case "Yakubu Gowon Crescent":
case "Kwame Nkuruma Street":
case "Lord Lugard Street":
case "Maitama Sule":
case "Thomas Sankara Street":
$district = "ASOKORO";
break;


case "Nairobi Street":
case "Daressalam Crescent":
case "Morovia Street":
case "Oda Cresent":
case "Parakou Street":
$district = "WUSE II";
break;



case "Oro Ago crescent":
case "Ubiaja":
case "Uke":
$district = "GARKI II";
break;



case "A. E. Akeinam street":
case "Ekukinam":
case "Dan Suleiman":
case "Festus Okotie Ibo Street":
case "IBN Haruna":
case "I.V.W Osisiogu Street":
case "Okonjo Iweala (Utako market)":
case "Shettima Mungolo":
$district = "UTAKO";
break;


case "Oladipo Diya":
$district = "GUDU";
break;


case "5TH Crescent":
case "Abdul Sallami":
case "Algiers":
case "Abali street":
case "Balanga":
case "Bengazi":
case "Embu Street":
case "Funmilayo Ransome Kutie":
case "Obafemi Awolowo":
case "Olusegun Obansanjo":
case "Onitsha Crescent":
case "Ibrahim Taiwo":
case "Jama'are Close":
case "J.J Oluleye Street":
case "Jimmy Carter Street":
case "Kitwe":
case "Kumo Street":
case "Kigoma":
case "Ladi Kwali":
case "Michael Okpara":
case "Mambolo":
case "Mombasa":
case "Nauakchatt":
case "Suez Crescent":
case "Udo Udoma":
case "Uruguay Street":
case "Zigunchor Kitwe":
$district = "UNKNOWN";
break;

default : $district = "NOT AVAILABLE"; 
}
?>