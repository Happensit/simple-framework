<?php

namespace Commty\Simple\ServiceProvider;

use Commty\Simple\Application;

/**
 * Interface ServiceProviderInterface
 * @package commty\ServiceProvider
 */
interface ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     * @param Application $app
     * @return
     */
    public function register(Application $app);
}
