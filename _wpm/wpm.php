<?php
// include composer's autoload file.
require __DIR__ . '/vendor/autoload.php';

// create global for WPM's container
global $wpmContainer;

// load the class map and create the wpm container
$wpmClassMap = include __DIR__ . '/config/classMap.php';
$wpmContainer = new \Wpm\WpmContainer(
    __DIR__,
    get_template_directory_uri() . '/' . basename(__DIR__),
    $wpmClassMap
);


/**
 * A shortcut function to obtain and return the wpmContainer
 * global
 *
 * @return \Wpm\WPMContainer
 */
function wpmContainer()
{
    global $wpmContainer;
    return $wpmContainer;
}


/**
 * wpm is the base function for all interactions with the
 * wpmContainer.
 *
 * @param $selector
 *
*@return bool|object
 * @internal param $selectorString
 */
function wpm($selector)
{
    return wpmContainer()->resolve((string) $selector);
}
