<?php

//Note: This file should be included first in every php page.
$db_conf = array();
$db_conf["type"] = "mysqli";
$db_conf["server"] = "localhost"; // or you mysql ip
$db_conf["user"] = "root"; // username
$db_conf["password"] = "root@2017"; // password
$db_conf["database"] = "admincore"; // database

// pass connection array to jqgrid()
//$g = new jqgrid($db_conf);