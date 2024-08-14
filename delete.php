<?php
require "db.php";
$obj=new Database;
$obj->delete($db, "users", $_GET['id']);