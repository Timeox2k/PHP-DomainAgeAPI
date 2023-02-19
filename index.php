<?php
use TimeoxTwok\DomainAgeApi\DomainAge;
error_reporting(0);
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
if (isset($_GET["domain"])) {
    $domainName = strtolower(htmlspecialchars($_GET["domain"]));
    $domainAge = new DomainAge($domainName);
    $domainAge->getDomainAge();
} else {
    echo "Error: The 'domain' parameter is missing from the URL. Please include a valid domain name to check its age.";
}

