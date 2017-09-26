<?php
namespace Legacy\LegacyDb;

/**
 * Adapter to statically call legacyDb methods
 * from legacy code
 */
class StaticLegacyDb {
    protected static $legacyDb;

    public static function setLegacyDb(LegacyDbInterface $legacyDb) {
        self::$legacyDb = $legacyDb;
    }

    public static function getLegacyDb() {
        return self::$legacyDb;
    }

    public static function mysqlConnect($host, $user, $password) {
        return self::$legacyDb->mysqlConnect($host, $user, $password);
    }

    public static function mysqlQuery($query, $linkId = null) {
        return self::$legacyDb->mysqlQuery($query, $linkId);
    }

    public static function mysqlSelectDb($database, $linkId = null) {
        return self::$legacyDb->mysqlSelectDb($database, $linkId);
    }

    public static function mysqlFetchAssoc($result) {
        return self::$legacyDb->mysqlFetchAssoc($result);
    }

    public static function mysqlFetchArray($result) {
        return self::$legacyDb->mysqlFetchArray($result);
    }

    public static function mysqlNumRows($result) {
        return self::$legacyDb->mysqlNumRows($result);
    }

    public static function mysqlClose($linkId = null) {
        return self::$legacyDb->mysqlClose();
    }

    public static function mysqlError($linkId = null) {
        return self::$legacyDb->mysqlError();
    }

    public static function mysqlEscapeString($str, $linkId = null) {
        return self::$legacyDb->mysqlEscapeString();
    }

    public static function mysqlRealEscapeString($str, $linkId = null) {
        return self::$legacyDb->mysqlRealEscapeString($str, $linkId);
    }

    public static function mysqlFreeResult($result) {
        return self::$legacyDb->mysqlFreeResult($result);
    }

    public static function mysqlInsertId($linkId = null) {
        return self::$legacyDb->mysqlInsertId();
    }
}
