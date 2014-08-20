<?php
include_once("include/config.php");

//session_destroy();

unset($_SESSION["ss_iduser"]);
unset($_SESSION["ss_username"]);
unset($_SESSION["ss_office_id"]);
unset($_SESSION["ss_user_group"]);
unset($_SESSION["ss_last_log"]);

header("Refresh: 0; url=index.php");
exit();
?>