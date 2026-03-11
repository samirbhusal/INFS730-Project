<?php
require_once 'functions.php';
startAppSession();

session_unset();
session_destroy();

redirectTo('login.php?logout=1');