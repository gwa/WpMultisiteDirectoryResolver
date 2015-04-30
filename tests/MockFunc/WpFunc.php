<?php

namespace Gwa\Wordpress;

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

function isFilterSet($filtername)
{
    // return true
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
