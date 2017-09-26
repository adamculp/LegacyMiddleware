<?php

namespace Legacy\LegacyDb;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class LegacyDbMysql implements LegacyDbInterface {

    protected $resourceIndex;

    protected $lastLink;

    public function __construct() {
        $this->resourceIndex = [];
    }

    public function mysqlConnect($host, $user, $password) {
        $link = mysql_connect($host, $user, $password);
        $this->resourceIndex[] = $link;
        $this->lastLink = $link;

        return $link;
    }

    public function mysqlQuery($query, $linkIdentifier = null) {
        return mysql_query($query, $this->getLastIfNull($linkIdentifier));
    }

    public function mysqlFetchAssoc($result) {
        return mysql_fetch_assoc($result);
    }

    public function mysqlFetchArray($result) {
        return mysql_fetch_array($result);
    }

    public function mysqlNumRows($result) {
        return mysql_num_rows($result);
    }

    public function mysqlSelectDb($database, $linkIdentifier = null) {
        return mysql_select_db($database, $this->getLastIfNull($linkIdentifier));
    }

    public function mysqlClose($linkIdentifier = null) {
        return mysql_close($this->getLastIfNull($linkIdentifier));
    }

    public function mysqlError($linkIdentifier = null) {
        return mysql_error($this->getLastIfNull($linkIdentifier));
    }

    public function mysqlRealEscapeString($str, $linkIdentifier = null) {
        return mysql_real_escape_string($str, $this->getLastIfNull($linkIdentifier));
    }

    public function mysqlEscapeString($str) {
        return $this->mysqlRealEscapeString($str);
    }

    public function mysqlInsertId($linkIndentifier = null) {
        return mysql_insert_id($this->getLastIfNull($linkIdentifier));
    }

    public function mysqlFreeResult($result) {
        return true;
        //return mysql_free_result($result);
    }

    protected function getLastIfNull($link) {
        if(is_null($link)) {
            return $this->lastLink;
        }
        return $link;
    }

    protected function matchLink($link) {
        foreach($this->resourceIndex as $idx) {
            if($idx === $link) {
                return $idx;
            }
        }
        return $this->lastLink;
    }
}
