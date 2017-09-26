<?php

namespace Legacy\Helper;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Helper class to provide a static facade to superglobal calls
 */

class Request {

    protected static $request;

    public static function setRequest(ServerRequestInterface $request) {
        self::$request = $request;
    }

    public static function getPostParam($param, $default = null) {
        $post = self::$request->getParsedBody();
        return isset($post[$param]) ? $post[$param] : $default;
    }
}
