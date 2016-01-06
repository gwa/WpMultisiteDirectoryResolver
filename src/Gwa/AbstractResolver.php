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

use Gwa\Wordpress\MockeryWpBridge\Traits\WpBridgeTrait;

/**
 * AbstractResolver.
 *
 * @author  Daniel Bannert
 */
abstract class AbstractResolver
{
    use WpBridgeTrait;

    /**
     * Folder path to wordpress, with trailing slash.
     *
     * @var string
     */
    protected $wpDirectoryPath = '';

    /**
     * Wordpress folder name.
     *
     * @var string
     */
    protected $wpFolderName = '';

    /**
     * MultisiteDirectoryResolver.
     *
     * @param string $wpdir
     */
    public function __construct($wpdir)
    {
        $this->wpDirectoryPath = substr($wpdir, -1) === '/' ? $wpdir : $wpdir.'/';
        $this->setWpFolderName();
    }

    /**
     * Set the right links in Adminbar.
     *
     * @param string $path
     * @param string $scheme
     *
     * @return string
     */
    public function fixNetworkAdminUrlFilter($path = '', $scheme = 'admin')
    {
        if (strpos($path, $this->wpDirectoryPath)) {
            return $path;
        }

        $wordpressUrl = [
            '/(wp-admin)/',
            '/(wp-login\.php)/',
            '/(wp-activate\.php)/',
            '/(wp-signup\.php)/',
        ];

        $multiSiteUrl = [
            $this->wpFolderName.'/wp-admin',
            $this->wpFolderName.'/wp-login.php',
            $this->wpFolderName.'/wp-activate.php',
            $this->wpFolderName.'/wp-signup.php',
        ];

        return preg_replace($wordpressUrl, $multiSiteUrl, $path, 1);
    }

    /**
     * Fix double backslashes in app folder.
     *
     * @param string
     */
    public function fixWpDoubleSlashFilter($urls)
    {
        foreach ($urls as &$url) {
            if ($url) {
                $url = str_replace('//app', '/app', $url);
            }
        }

        return $urls;
    }

    /**
     * Fixes the protocol in urls. Replaces leading double slashes //
     * with the full protocol; https or http depending on context.
     *
     * @param string
     *
     * @return array
     */
    public function fixWpProtocolFilter($urls)
    {
        $protocol = $this->getSiteProtocol();

        foreach ($urls as $k => &$v) {
            if ((strpos($k, 'url') !== false) && (substr($v, 0, 2) === '//')) {
                $v = $protocol.ltrim($v, '//');
            }
        }

        return $urls;
    }

    /**
     * Get the correct protocol.
     *
     * @return string
     */
    protected function getSiteProtocol()
    {
        return $this->getWpBridge()->isSsl() ? 'https://' : 'http://';
    }

    /**
     * Init all filter.
     */
    public function init()
    {
        $this->getWpBridge()->addFilter('network_admin_url', [$this, 'fixNetworkAdminUrlFilter'], 10, 2);

        $this->getWpBridge()->addFilter('script_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);
        $this->getWpBridge()->addFilter('style_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);

        $this->getWpBridge()->addFilter('upload_dir', [$this, 'fixWpDoubleSlashFilter'], 10, 1);
        $this->getWpBridge()->addFilter('upload_dir', [$this, 'fixWpProtocolFilter'], 10, 1);
    }

    /**
     * Set wordpress folder name.
     *
     * @param string
     */
    protected function setWpFolderName()
    {
        $dirs = explode('/', $this->wpDirectoryPath);

        $this->wpFolderName = $dirs[count($dirs) - 2];
    }
}
