<?php
namespace Legacy;

class ConfigProvider {
    
    public function __invoke() {
        return [
            'legacy_routes' => $this->getLegacyRoutes(),
            'dependencies' => $this->getDependencies(),
        ];
    }
    
    public function getLegacyRoutes() {
        return require_once(__DIR__.'/config/legacy-routes.php');
    }
    
    public function getDependencies()
    {
        return [
            'invokables' => [
                LegacyDb\LegacyDbMysql::class => LegacyDb\LegacyDbMysql::class,
                LegacyDb\LegacyDbMysqli::class => LegacyDb\LegacyDbMysqli::class,
            ],
            'factories'  => [
                Legacy::class => LegacyFactory::class,
            ],
        ];
    }
}
