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
 *
 * @version     0.0.3-dev
 */

/**
 * MultisiteDirectoryResolver.
 *
 * @author  Daniel Bannert
 */
class MultisiteDirectoryResolver
{
    /**
     * Network Url.
     *
     * @type string
     */
    protected $networkUrl = '';

    /**
     * Folder path to wordpress.
     *
     * @type string
     */
    protected $cmWpDir = '';

    /**
     * Wordpress folder name.
     *
     * @type string
     */
    protected $wpFolderName = '';

    /**
     * Wp dir path.
     *
     * @type string
     */
    protected $const = '';

    /**
     * MultisiteDirectoryResolver.
     *
     * @param string $const
     * @param string $networkUrl
     */
    public function __construct($const = '', $networkUrl = null)
    {
        $this->const   = $const;

        $this->checkForDefinition();

        $this->cmWpDir = substr($this->const, -1) === '/' ? $this->const : $this->const.'/';

        $this->setWpFolderName();
        $this->setNetworkUrl($networkUrl);
    }

    /**
     * Set the right links in Adminbar.
     *
     * @param string $path
     * @param string $scheme
     *
     * @return stromg
     */
    public function fixNetworkAdminUrlFilter($path = '', $scheme = 'admin')
    {
        if (strpos($path, $this->cmWpDir)) {
            return $path;
        }

        $wordpressUrl = ['/(wp-admin)/'];
        $multiSiteUrl = [$this->wpFolderName.'/wp-admin'];

        return preg_replace($wordpressUrl, $multiSiteUrl, $path, 1);
    }

    /**
     * Set the right path for wp-login and wp-admin.
     *
     * @param string      $url
     * @param string      $path
     * @param string|null $scheme
     *
     * @return string
     */
    public function fixSiteUrlFilter($url, $path, $scheme)
    {
        if (strpos($url, $this->cmWpDir)) {
            return $url;
        }

        $wordpressUrl = ['/(wp-login\.php)/', '/(wp-admin)/'];
        $multiSiteUrl = [$this->cmWpDir.'wp-login.php', $this->cmWpDir.'wp-admin'];

        return preg_replace($wordpressUrl, $multiSiteUrl, $url, 1);
    }

    /**
     * Set the right path for script and style loader.
     *
     * @param string $src
     * @param string $handle
     *
     * @return string
     */
    public function fixStyleScriptPathFilter($src, $handle)
    {
        $dir = rtrim($this->cmWpDir, '/');

        if (
            strpos($src, site_url()) !== false &&
            strpos($src, 'plugins') === false &&
            strpos($src, $dir) === false
        ) {
            $styleUrl = explode(site_url(), $src);
            $src = site_url().'/'.$dir.$styleUrl[1];
        }

        return esc_url($src);
    }

    /**
     * Add subfolder path to wp-includes path.
     *
     * @param string $url
     * @param string $path
     *
     * @return string
     */
    public function fixWpIncludeFolder($url, $path)
    {
        if (strpos($url, $this->cmWpDir)) {
            return $url;
        }

        $wordpressUrl = ['/(wp-includes)/'];
        $multiSiteUrl = [$this->cmWpDir.'wp-includes'];

        return preg_replace($wordpressUrl, $multiSiteUrl, $url, 1);
    }

    /**
     * Init all filter.
     */
    public function init()
    {
        add_filter('network_admin_url', [$this, 'fixNetworkAdminUrlFilter'], 10, 2);
        add_filter('site_url', [$this, 'fixSiteUrlFilter'], 10, 3);

        add_filter('script_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);
        add_filter('style_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);

        add_filter('includes_url', [$this, 'fixWpIncludeFolder'], 10, 2);
    }

    /**
     * Check if define for "CM_WP_DIR" is set.
     *
     * @throws \Exception
     */
    protected function checkForDefinition()
    {
        if ($this->const === '') {
            throw new \Exception('Please set the path to, where your Wordpress folder is.');
        }
    }

    /**
     * Set network url.
     *
     * @param string
     */
    protected function setNetworkUrl($networkUrl)
    {
        $this->networkUrl = $networkUrl ?: $this->wpFolderName.'/wp-admin/network/';
    }

    /**
     * Set wordpress folder name.
     *
     * @param string
     */
    protected function setWpFolderName()
    {
        $wpFolderName       = explode('/', $this->cmWpDir);

        $this->wpFolderName = $wpFolderName[count($wpFolderName) - 2];
    }
}
