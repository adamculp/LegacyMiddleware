<?php

namespace Legacy\LegacyDb;

use Interop\Container\ContainerInterface;

class LegacyDbFactory {

    public function __invoke(ContainerInterface $container) {
        return new LegacyDb();
    }

}
