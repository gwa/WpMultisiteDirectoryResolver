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
 * AbstractResolver.
 *
 * @author  Daniel Bannert
 */
abstract class AbstractResolver
{
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
        add_filter('network_admin_url', [$this, 'fixNetworkAdminUrlFilter'], 10, 2);

        add_filter('script_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);
        add_filter('style_loader_src', [$this, 'fixStyleScriptPathFilter'], 10, 2);

        add_filter('upload_dir',        [$this, 'fixWpDoubleSlashFilter'], 10, 1);
        add_filter('upload_dir',        [$this, 'fixWpProtocolFilter'], 10, 1);
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

    /**
     * Fix double backslashes in app folder.
     *
     * @param string
     */
    public function fixWpDoubleSlashFilter($urls)
    {
        foreach ($urls as &$url)
        {
            $url = str_replace('//app', '/app', $url);
        }

        return $urls;
    }

    /**
     * Fix protocol in urls
     *
     * @param string
     * @return array
     */
    public function fixWpProtocolFilter($urls)
    {
        $protocol = self::getSiteProtocol();

        $urls['url']     = $protocol.ltrim($urls['url'], '//');
        $urls['baseurl'] = $protocol.ltrim($urls['baseurl'], '//');

        return $urls;
    }

    /**
     * Get the correct protocol
     *
     * @param string
     * @return string
     */
    protected static function getSiteProtocol()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    }
}
