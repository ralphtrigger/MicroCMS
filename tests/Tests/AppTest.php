<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MicroCMS\Tests;

use Silex\WebTestCase;

require_once __DIR__.'/../../vendor/autoload.php';

/**
 * Description of AppTest
 *
 * @author trigger
 */
class AppTest extends WebTestCase
{
    /**
     * Basic, application-wide functional test inspired by Symfony best practices.
     * Simply checks that all application URL load succesfully.
     * During test execution, this method is called for each URL return by the providerURLs method.
     * 
     * @dataProvider provideURLs
     */
    public function testPageIsSuccesful($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * {@inheritDoc}
     */
    public function createApplication()
    {
        $app = new \Silex\Application();

        require __DIR__.'/../../app/config/dev.php';
        require __DIR__.'/../../app/app.php';
        require __DIR__.'/../../app/routes.php';

        // Generate raw exceptions instead of html page if errors occur
        $app['exception_handler']->disable();
        // Simulate session for testing
        $app['session.test'] = true;
        // Enable anonymous acces to admin zone
        $app['security.access_rules'] = array();

        return $app;
    }

    /**
     * Provide all valid application URLs.
     * 
     * @return array The list of all valid application URLs.
     */
    public function provideURLs()
    {
        return array(
            array('/'),
            array('/article/1'),
            array('/login'),
            array('/admin'),
            array('/admin/article/add'),
            array('/admin/article/1/edit'),
            array('/admin/comment/1/edit'),
            array('/admin/user/add'),
            array('/admin/user/1/edit'),
        );
    }

}
