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
 * MultisiteSubDomainResolver.
 *
 * @author  Daniel Bannert
 */
class MultisiteSubDomainResolver extends AbstractResolver implements ResolverContract
{
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
        if (strpos($src, '/app')) {
            $src = str_replace('//app', '/app', $src);
        }

        return esc_url($src);
    }
}
