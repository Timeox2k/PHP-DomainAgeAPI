<?php
use TimeoxTwok\DomainAgeApi\DomainAge;
error_reporting(0);
require './vendor/autoload.php';
if (isset($_GET["domain"])) {
    $domainName = strtolower(htmlspecialchars($_GET["domain"]));
    $domainAge = new DomainAge($domainName);
    $domainAge->getDomainAge();
}

