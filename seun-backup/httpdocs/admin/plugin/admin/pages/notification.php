<?php
$admin_notification = $engine->admin_notification()
?>
<div id="adminmenu_holder">
<div class="admenu"><span id="aduserspan"></span>Admin <div id="aduser" class="adminmenu_counter"><?php echo $admin_notification['adminusers'];?></div></div>
<div class="admenu"><span id="adwidgetspan"></span>Widget <div id="adwidget" class="adminmenu_counter"><?php echo $admin_notification['widget'];?></div></div>
<div class="admenu"><span id="adpluginspan"></span>Plugin <div id="adplugin" class="adminmenu_counter"><?php echo $admin_notification['plugin'];?></div></div>
<div class="admenu"><span id="adbackupspan"></span>Last Backup <div id="adbackup" class="adminmenu_counter"><?php echo $admin_notification['last_backup'];?></div></div>
<div class="admenu"><span id="addiskspan"></span>Disk Usage <div id="addisk" class="adminmenu_counter"><?php echo $admin_notification['usedspace'];?></div></div>
</div>