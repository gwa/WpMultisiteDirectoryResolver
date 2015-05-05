<?php

namespace Gwa\Wordpress;

/**
 * Composer Multisite fixer.
 *
 * @author      Daniel Bannert <bannert@greatwhiteark.com>
 * @copyright   2015 Great White Ark
 *
 * @link        http://www.greatwhiteark.com
 *
 * @license     MIT
 *
 * @version     0.0.4
 */

$addedFilters = [];

function add_filter($filterName, $filterCall, $prio, $numVars)
{
    global $addedFilters;

    $data = new \stdClass();
    $data->filtername = $filterName;
    $data->callback = $filterCall;
    $data->prio = $prio;
    $data->numvars = $numVars;

    $addedFilters[] = $data;
}

function getAddedFilters()
{
    global $addedFilters;

    return $addedFilters;
}

/* -------- */

$setConstants = [];

function site_url()
{
    return 'http://example.org/projects/testWordpress/';
}

function esc_url($url)
{
    return $url;
}
