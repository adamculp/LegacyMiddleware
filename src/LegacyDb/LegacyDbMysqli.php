<?php

namespace Legacy\LegacyDb;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class LegacyDbMysqli implements LegacyDbInterface {

    protected $resourceIndex;

    protected $lastLink;

    public function __construct() {
        $this->resourceIndex = [];
    }

    public function mysqlConnect($host, $user, $password) {
        $link = mysqli_connect($host, $user, $password);
        $this->resourceIndex[] = $link;
        $this->lastLink = $link;

        return $link;
    }

    public function mysqlQuery($query, $linkIdentifier = null) {
        return mysqli_query($this->getLastIfNull($linkIdentifier), $query);
    }

    public function mysqlFetchAssoc($result) {
        return mysqli_fetch_assoc($result);
    }

    public function mysqlFetchArray($result) {
        return mysqli_fetch_array($result);
    }

    public function mysqlNumRows($result) {
        return mysqli_num_rows($result);
    }

    public function mysqlSelectDb($database, $linkIdentifier = null) {
        return mysqli_select_db($this->getLastIfNull($linkIdentifier), $database);
    }

    public function mysqlClose($linkIdentifier = null) {
        $res = mysqli_close($this->getLastIfNull($linkIdentifier));
        $this->lastLink = null;
        return $res;
    }

    public function mysqlError($linkIdentifier = null) {
        return mysqli_error($this->getLastIfNull($linkIdentifier));
    }

    public function mysqlRealEscapeString($str, $linkIdentifier = null) {
        return mysqli_real_escape_string($this->getLastIfNull($linkIdentifier), $str);
    }

    public function mysqlEscapeString($str) {
        return $this->mysqlRealEscapeString($str);
    }

    public function mysqlInsertId($linkIndentifier = null) {
        return mysqli_insert_id($this->getLastIfNull($linkIdentifier));
    }

    public function mysqlFreeResult($result) {
        return true;
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
