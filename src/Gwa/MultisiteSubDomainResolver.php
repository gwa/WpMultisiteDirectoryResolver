<?php
namespace Gwa\Wordpress;

use Gwa\Wordpress\Contracts\MultisiteDirectoryResolver as ResolverContract;

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

        return $this->getWpBridge()->escUrl($src);
    }
}
