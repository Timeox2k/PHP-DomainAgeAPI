<?php
error_reporting(0);
require './vendor/autoload.php';
if (isset($_GET["domain"])) {
    $domainName = strtolower(htmlspecialchars($_GET["domain"]));
    DomainAge::getDomainAge($domainName);
}

