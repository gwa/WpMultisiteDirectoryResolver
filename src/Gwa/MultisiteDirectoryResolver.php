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
class MultisiteDirectoryResolver extends AbstractResolver implements ResolverContract
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
        $this->wpDirectoryPath = substr($wpdir, -1) === '/' ? $wpdir : $wpdir.'/';;

        $this->setWpFolderName();
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
        $multiSiteUrl = [trim($this->wpDirectoryPath, '/').'/wp-login.php', trim($this->wpDirectoryPath, '/').'/wp-admin'];

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
            $src = site_url().$dir.$styleUrl[1];
        }

        if (strpos($src, '/app')) {
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
        $multiSiteUrl = [trim($this->wpDirectoryPath, '/').'/wp-includes'];

        return preg_replace($wordpressUrl, $multiSiteUrl, $url, 1);
    }

    public function init()
    {
        parent::init();

        add_filter('site_url', [$this, 'fixSiteUrlFilter'], 10, 3);
        add_filter('includes_url', [$this, 'fixWpIncludeFolder'], 10, 2);
    }
}
