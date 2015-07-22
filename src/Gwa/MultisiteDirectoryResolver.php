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

/**
 * MultisiteDirectoryResolver.
 *
 * @author  Daniel Bannert
 */
class MultisiteDirectoryResolver
{
    /**
     * Folder path to wordpress, with trailing slash.
     *
     * @type string
     */
    protected $wpDirectoryPath = '';

    /**
     * Wordpress folder name.
     *
     * @type string
     */
    protected $wpFolderName = '';

    /**
     * MultisiteDirectoryResolver.
     *
     * @param string $wpdir
     */
    public function __construct($wpdir)
    {
        if (!is_string($wpdir) || $wpdir === '') {
            throw new \Exception('Please set the relative path to your Wordpress install folder.');
        }

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
        if (strpos($url, $this->wpDirectoryPath)) {
            return $url;
        }

        $wordpressUrl = ['/(wp-login\.php)/', '/(wp-admin)/'];
        $multiSiteUrl = [$this->wpDirectoryPath.'wp-login.php', $this->wpDirectoryPath.'wp-admin'];

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
        $dir = rtrim($this->wpDirectoryPath, '/');

        if (
            strpos($src, site_url()) !== false &&
            strpos($src, 'plugins') === false &&
            strpos($src, $dir) === false
        ) {
            $styleUrl = explode(site_url(), $src);
            $src = site_url().'/'.$dir.$styleUrl[1];
        }

        if (strpos($src, 'plugins') && strpos($src, '/app')) {
            $src = str_replace('//app', '/app', $src);
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
        if (strpos($url, $this->wpDirectoryPath)) {
            return $url;
        }

        $wordpressUrl = ['/(wp-includes)/'];
        $multiSiteUrl = [$this->wpDirectoryPath.'wp-includes'];

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
     * Set wordpress folder name.
     *
     * @param string
     */
    protected function setWpFolderName()
    {
        $folders = explode('/', $this->wpDirectoryPath);

        $this->wpFolderName = $folders[count($folders) - 2];
    }
}
