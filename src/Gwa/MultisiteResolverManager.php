<?php
namespace Gwa\Wordpress;

use Exception;
use Gwa\Wordpress\WpBridge\Traits\WpBridgeTrait;
use Gwa\Wordpress\WpBridge\WpBridge;

class MultisiteResolverManager
{
    use WpBridgeTrait;

    const TYPE_SUBDOMAIN = '\Gwa\Wordpress\MultisiteSubDomainResolver';
    const TYPE_FOLDER = '\Gwa\Wordpress\MultisiteDirectoryResolver';

    /**
     * Resolver Handler.
     *
     * @var \Gwa\Wordpress\Contracts\MultisiteDirectoryResolver
     */
    protected $handler;

    /**
     * MultisiteResolverManager.
     *
     * @param string $wpdir
     * @param string $multisiteDomainType
     * @param null   $wpBridge
     */
    public function __construct($wpdir, $multisiteDomainType, $wpBridge = null)
    {
        if (!is_string($wpdir) || $wpdir === '' || $wpdir === '/') {
            throw new Exception('Please set the relative path to your Wordpress install folder.');
        }

        $this->setWpBridge(($wpBridge !== null) ? $wpBridge : new WpBridge());

        $handler = new $multisiteDomainType($wpdir);
        $handler->setWpBridge($this->getWpBridge());

        $this->handler = $handler;
    }

    /**
     * Get current Handler.
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
