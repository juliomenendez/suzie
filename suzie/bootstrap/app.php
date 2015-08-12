<?php

/**
 * Build root path.
 */
$rootPath = realpath(__DIR__.'/../..');

/*
 * Fetch .env
 */
if (file_exists($rootPath.'/.env')) {
    Dotenv::load($rootPath);
}

/*
 * Check if getenv is not working
 */
if (getenv('APP_ENV') == false) {
    die('Check your settings, you may need to add a .env file');
}

/***
 * Mailer
 */
function wp_mail($to, $subject, $message, $headers = '', $attachments = [])
{
    return Suzie\Mailer::boot($to, $subject, $message, $headers, $attachments);
}

function suzie()
{
    /*
     * Sync
     */
    $sync = new Suzie\Sync();

    /*
     * Cdn
     */
    new Suzie\Cdn($sync->remoteUrl);

    /*
     * Varnish
     */
    new Suzie\Varnish();

    /*
    * Schedule Route
    */
    add_action('parse_request', function ($wp)
    {
        if (isset($_GET['suzie']))
        {
            $name = $_GET['suzie'];

            if ($name == 'scheduler' &&
                getenv('SCHEDULER_ROUTE') == 'true' &&
                getenv('SCHEDULER_TOKEN') == $_GET['token'])
            {
                include 'scheduler.php';
            }

            die(0);
        }
    });

    /*
    * Set a global object for sync incase a plugin needs it
    */
    global $suzie_sync;
    $suzie_sync = $sync;

    do_action('suzie');
}
