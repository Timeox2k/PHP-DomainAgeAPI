<?php

error_reporting(0);

use Iodev\Whois\Factory;

require './vendor/autoload.php';
require($_SERVER['DOCUMENT_ROOT'] . "/database.php");

if (isset($_GET["domain"])) {
    $domainName = strtolower(htmlspecialchars($_GET["domain"]));

    $stmt = Database::getInstance()->prepare("SELECT * FROM domain_data where name = :name LIMIT 1");
    $stmt->bindParam(":name", $domainName);
    $stmt->execute();
    $domainCount = $stmt->rowCount();
    $databaseDomainData = $stmt->fetch();

    if ($domainCount === 1) {
        $databaseEntryTime = strtotime($databaseDomainData["entryTime"]);
        $time30DaysAgo = strtotime('-30 days');

        if ($databaseEntryTime < $time30DaysAgo) {
            output($databaseDomainData["age"]);
            exit;
        }
    }

    $whois = Factory::get()->createWhois();
    try {
        $response = $whois->loadDomainInfo($domainName);

        if (!$response->creationDate) {
            $timestamp = $response->creationDate;
        } else {
            $timestamp = $response->updatedDate;
        }
        output($databaseDomainData["age"]);
        $entryTime = time();
        if ($domainCount == 1) {
            $stmt = Database::getInstance()->prepare("UPDATE domain_data SET age = ?, entryTime = ? WHERE name = ? LIMIT 1");
            $stmt->execute([$timestamp, $entryTime, $domainName]);
        } else {
            $stmt = Database::getInstance()->prepare("INSERT INTO domain_data (name,entryTime,age) VALUES (:name, :entry_time, :age);");
            $stmt->bindParam(":name", $domainName);
            $stmt->bindParam(":entry_time", $entryTime);
            $stmt->bindParam(":age", $timestamp);
            $stmt->execute();
        }
    } catch (Exception $e) {
        output('Domain not found. / Something went wrong', 500);
    }

}

function output($data, $code = 200)
{
    http_response_code($code);
    header("Content-Type: application/json");
    $json = [
        "code" => $code,
        "success" => $code === 200,
        "message" => $data
    ];
    echo json_encode($json);
}