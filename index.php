<?php
error_reporting(0);
require_once('DomainAge.php');
if (isset($_GET["domain"])) {
    $domainName = strtolower(htmlspecialchars($_GET["domain"]));
    DomainAge::getDomainAge($domainName);
}

