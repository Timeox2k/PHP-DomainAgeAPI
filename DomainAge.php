<?php

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;

class DomainAge
{

    public static function getDomainAge($domainName)
    {
        require_once('Database.php');
        $stmt = Database::getInstance()->prepare("SELECT * FROM domain_data where name = :name LIMIT 1");
        $stmt->bindParam(":name", $domainName);
        $stmt->execute();
        $domainCount = $stmt->rowCount();
        $databaseDomainData = $stmt->fetch();

        if ($domainCount === 1) {
            $databaseEntryTime = strtotime($databaseDomainData["entryTime"]);
            $time30DaysAgo = strtotime('-30 days');

            if ($databaseEntryTime < $time30DaysAgo) {
                self::output($databaseDomainData["age"]);
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

            if ($timestamp > 10) {
                self::storeToDatabase($domainName, $timestamp);
                self::output($timestamp);
            } else {
                self::output('WHOIS does not provide any Information about the creation- or update- Date', 404);
            }

        } catch (WhoisException|ConnectionException|ServerMismatchException $e) {
            self::output('Domain not found. / Something went wrong', 404);
        } 

    }

    private static function storeToDatabase($domainName, $timestamp)
    {
        require_once('Database.php');
        $entryTime = time();
        $stmt = Database::getInstance()->prepare("SELECT * FROM domain_data where name = :name LIMIT 1");
        $stmt->bindParam(":name", $domainName);
        $stmt->execute();
        $domainCount = $stmt->rowCount();
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
    }

    private static function output($data, $code = 200)
    {

        http_response_code($code);
        header("Content-Type: application/json");
        $json = [
            "code" => $code,
            "success" => $code === 200,
            "message" => $data
        ];
        echo json_encode($json);
        die;
    }
}
