<?php

namespace Legacy\LegacyDb;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

interface LegacyDbInterface {

    public function mysqlConnect($host, $user, $password);

    public function mysqlQuery($query, $linkIdentifier = null);

    public function mysqlFetchAssoc($result);

    public function mysqlFetchArray($result);

    public function mysqlNumRows($result);

    public function mysqlSelectDb($database, $linkIdentifier = null);

    public function mysqlClose($linkIdentifier = null);

    public function mysqlError($linkIdentifier = null);

    public function mysqlRealEscapeString($str, $linkIdentifier = null);

    public function mysqlEscapeString($str);

    public function mysqlInsertId($linkIndentifier = null);

    public function mysqlFreeResult($result);
}
