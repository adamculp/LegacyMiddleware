<?php

namespace Legacy;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouteResult;
use Legacy\LegacyDb\StaticLegacyDb as SLDb;
use Legacy\Helper\Request as RequestHelper;
use Doctrine\Common\EventManager;

class Legacy implements MiddlewareInterface {
    protected $legacyRoutes;
    protected $legacyDb;
    protected $evm;

    public function __construct($legacyRoutes, $legacyDb, EventManager $evm) {
        $this->legacyRoutes = $legacyRoutes;
        $this->legacyDb = $legacyDb;
        $this->evm = $evm;
    }

    public function process(Request $request, DelegateInterface $delegate)
    {
        $legacyFile = $this->getLegacyRoute($request);

        //If URL passed had query string and other parts, make them into a new URI and call this middleware again
        $newRequest = $this->splitUriAndProcess($legacyFile, $request, $delegate);
        if(!is_null($newRequest)) {
            return $this->process($newRequest, $delegate);
        }

        //Before passing on to the next middleware, try adding .php
        //to the path and reprocess.
        //TODO: refactor into function
        $phpReq = $this->addPhpExtension($request);
        if(!is_null($phpReq) && !$legacyFile) {
            return $this->process($phpReq, $delegate);
        }

        if ( $request->getAttribute(RouteResult::class) || !$legacyFile ) {
            return $delegate->process($request);
        }


        //Inject the statis legacyDb with an instance of LegacyDb to be used in the app
        $legacyDb = $this->legacyDb;
        SLDb::setLegacyDb($legacyDb);

        //Inject static Request helper with request
        RequestHelper::setRequest($request);

        //Inject GET params if they came through routing
        $_GET = array_merge($_GET, $request->getQueryParams());
        $legacyAppPath = realpath(dirname(__FILE__)."/../../../../public_html");

        if(!file_exists("$legacyAppPath$legacyFile")) {
            //No file found, move on to the next MW (404)
            return $delegate->process($request, $delegate);
        }

        //Match old application's route.
        //if php file, proceed, otherwise next.
        $docroot = $_SERVER['DOCUMENT_ROOT'];
        $_SERVER['DOCUMENT_ROOT'] = $legacyAppPath;

        //also change dir for relative image uploads and such
        $preCwd = getcwd();
        chdir($legacyAppPath);

        //OB legacy response
        ob_start();

        //Froward slash is included in the legacyFile
        require_once("$legacyAppPath$legacyFile");
        $response = ob_get_contents();
        ob_end_clean();

        //Restore DOCUMENT_ROOT and cwd
        $_SERVER['DOCUMENT_ROOT'] = $docroot;
        chdir($preCwd);

        //need to capture response's HTTP response code in case it's a redirect
        //specifically doens't work following a POST
        $code = http_response_code();

        return new HtmlResponse($response, $code);
    }

    public function checkIfPhp(Request $request) {
        $path = $request->getUri()->getPath();
        $parts = explode('/', $path);
        $last = array_pop($parts);

        return (false !== stripos($last, ".php"));
    }

    public function matchLegacyRoute(Request $request) {
        $legacyRoutes = $this->legacyRoutes;

        //Remove trailing slash
        $path = $this->removeTrailingSlash($request->getUri()->getPath());

        //single formward slash
        if(array_key_exists('/', $legacyRoutes) && $path == '/') {
            return $legacyRoutes['/'];
        }

        //Remove forward slash from list to avoid tripping up 
        if(array_key_exists('/', $legacyRoutes)) {
            unset($legacyRoutes['/']);
        }

        foreach($legacyRoutes as $pattern => $replacement) {
            $count = 0;
            $pattern = str_replace('/', "\\/", $pattern);
            $pattern = "/^$pattern\$/";
            if(preg_match($pattern, $path)) {
                $newPath = preg_replace($pattern, $replacement, $path);

                //Always return forward slash
                $this->addForwardSlash($newPath);
                return $newPath;
            }
        }
        return null;
    }

    /**
     * remove trailing slash, except if request consists only of a slash
     */
    protected function removeTrailingSlash($path) {
        if(strcmp($path, '/') === 0){
            return $path;
        }
        return rtrim($path);
    }

    protected function addForwardSlash($path) {
        if(!strpos($path, '/')) {
            return "/$path";
        }
    }
    
    protected function getLegacyRoute(Request $request) {
        if($this->checkIfPhp($request)) {
            return $request->getUri()->getPath();
        }
        $route = $this->matchLegacyRoute($request);
        return $route ? : false;
    }

    //Returns a new request with path having .php extension added if not present
    //otherwise return null
    protected function addPhpExtension(Request $request) {
        if($this->checkIfPhp($request)) {
            return null;
        }

        $uri =  $request->getUri();
        return $request->withUri($uri->withPath(rtrim($uri->getPath(), '/').".php"));
    }

    protected function splitUriAndProcess($legacyFile, $request, $delegate) {
        $parts = parse_url($legacyFile);
        $qs = [];


        if(count($parts) > 1) {
            $uri = $request->getUri();
            foreach($parts as $k => $v) {
                $function = 'with'.ucfirst($k);
                $uri = $uri->$function($v);
                if($k == 'query') {
                    parse_str($v, $qs);
                }
            }
            $request = $request->withUri($uri);
            $request = $request->withQueryParams($qs);

            return $request;
        }
        return null;
    }
}
