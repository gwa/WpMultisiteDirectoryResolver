<?php
namespace Gwa\Wordpress\Contracts;

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

/**
 * MultisiteDirectoryResolver.
 *
 * @author  Daniel Bannert
 */
Interface MultisiteDirectoryResolver
{
    /**
     * Set the right links in Adminbar.
     *
     * @param string $path
     * @param string $scheme
     *
     * @return string
     */
    public function fixNetworkAdminUrlFilter($path = '', $scheme = 'admin');

    /**
     * Set the right path for wp-login and wp-admin.
     *
     * @param string      $url
     * @param string      $path
     * @param string|null $scheme
     *
     * @return string
     */
    public function fixSiteUrlFilter($url, $path, $scheme);

    /**
     * Set the right path for script and style loader.
     *
     * @param string $src
     * @param string $handle
     *
     * @return string
     */
    public function fixStyleScriptPathFilter($src, $handle);

    /**
     * Add subfolder path to wp-includes path.
     *
     * @param string $url
     * @param string $path
     *
     * @return string
     */
    public function fixWpIncludeFolder($url, $path);
}
