<?php
namespace Gwa\Wordpress;

/**
 * Wordpress Multisite fixer.
 *
 * @author      Daniel Bannert <bannert@greatwhiteark.com>
 * @copyright   2015 Great White Ark
 *
 * @link        http://www.greatwhiteark.com
 *
 * @license     MIT
 */

use Gwa\Wordpress\MockeryWpBridge\MockeryWpBridge;

/**
 * MultisiteResolverManager.
 *
 * @author  Daniel Bannert
 */
class MultisiteResolverManager
{
    const TYPE_SUBDOMAIN = '\Gwa\Wordpress\MultisiteSubDomainResolver';
    const TYPE_FOLDER    = '\Gwa\Wordpress\MultisiteDirectoryResolver';

    /**
     * Resolver Handler
     *
     * @var \Gwa\Wordpress\Contracts\MultisiteDirectoryResolver
     */
    protected $handler;

    /**
     * MultisiteResolverManager.
     *
     * @param string $wpdir
     * @param string $multisiteDomainType
     */
    public function __construct($wpdir, $multisiteDomainType) {
        if (!is_string($wpdir) || $wpdir === '' || $wpdir === '/') {
            throw new \Exception('Please set the relative path to your Wordpress install folder.');
        }

        $bridge  = new WpBridge();

        $handler = new $multisiteDomainType($wpdir);
        $handler->setWpBridge($bridge);

        $this->handler = $handler;
    }

    /**
     * Get current Handler
     *
     * @return \Gwa\Wordpress\Contracts\MultisiteDirectoryResolver
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Init all filter.
     */
    public function init()
    {
        $this->handler->init();
    }
}
