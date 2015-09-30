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
     * Init all filter.
     */
    public function init()
    {
        $this->getWpBridge()->addFilter('network_admin_url', [$this, 'fixNetworkAdminUrlFilter'], 10, 2);

        $this->getWpBridge()->addFilter('script_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);
        $this->getWpBridge()->addFilter('style_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);
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
