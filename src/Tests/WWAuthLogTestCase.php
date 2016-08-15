<?php

namespace Drupal\ww_auth_log\Tests;

use Drupal\simpletest\WebTestBase;

/**
* Tests ww_auth_log is logging user events.
*
* @group __ww__
**/
class WWAuthLogTestCase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['ww_auth_log'];

  /**
  * Failed login attempts should be logged to ../logs/drupal_auth.log.
  **/
  public function testFailedLogin() {
    // Create unknown login post
    $form = array();
    $username = 'ww_auth_log.test_'.$this->randomMachineName();
    $form['name'] = $username;
    $form['pass'] = $this->randomMachineName();
    $this->drupalPostForm('user/login', $form, t('Log in'));

    // sanity check it failed
    $this->assertText(t('Unrecognized username or password. Have you forgotten your password?'));

    // check the log TODO: unfortunately d8 no longer logs the username, need to
    // find another way to check for the log entry created from this test
    // explicitly if the log is not empty
    $regex = "/.*Login attempt failed from/";
    $log = file_get_contents(DRUPAL_ROOT.'/../logs/drupal_auth.log');
    $this->assertTrue(preg_match($regex, $log));
  }
}
