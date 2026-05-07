<?php
require_once 'config.php';
session_destroy();
redirect('/roadside_ally_php/login.php');
?>