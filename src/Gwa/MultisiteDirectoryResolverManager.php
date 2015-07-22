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

use Gwa\Wordpress\Contracts\MultisiteDirectoryResolver as ResolverContract;

/**
 * MultisiteDirectoryResolver.
 *
 * @author  Daniel Bannert
 */
class MultisiteDirectoryResolverManager implements ResolverContract
{
    const TYPE_SUBDOMAIN = '\Gwa\Wordpress\MultisiteSubDirectoryResolver';
    const TYPE_FOLDER    = '\Gwa\Wordpress\MultisiteDirectoryResolver';

    /**
     * Resolver Handler
     *
     * @var \Gwa\Wordpress\Contracts\MultisiteDirectoryResolver
     */
    protected $handler;

    /**
     * MultisiteDirectoryResolver.
     *
     * @param string $wpdir
     * @param string $multisiteDomainType
     */
    public function __construct($wpdir, $multisiteDomainType) {
        if (!is_string($wpdir) || $wpdir === '') {
            throw new \Exception('Please set the relative path to your Wordpress install folder.');
        }

        $this->handler = new $multisiteDomainType($wpdir);
    }

    /**
     * Init all filter.
     */
    public function init()
    {
        add_filter('network_admin_url', [$this->handler, 'fixNetworkAdminUrlFilter'], 10, 2);
        add_filter('site_url', [$this->handler, 'fixSiteUrlFilter'], 10, 3);

        add_filter('script_loader_src', [$this->handler, 'fixStyleScriptPathFilter'], 10, 2);
        add_filter('style_loader_src', [$this->handler, 'fixStyleScriptPathFilter'], 10, 2);

        add_filter('includes_url', [$this->handler, 'fixWpIncludeFolder'], 10, 2);
    }
}
