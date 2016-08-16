<?php

namespace Drupal\ww_auth_log\Tests;

use Drupal\simpletest\WebTestBase;

/**
* Run the WWAuthLogTestCase multiple times to trigger fail2ban without having to
* run the test several times and incur the long setup time for each.
*
* NOTE: a network interface must exist for 1.2.3.4 i.e.
*       'sudo ifconfig eth0:123 1.2.3.4'
*
* @group __ww__
**/
class WWAuthLogFail2BanTestCase extends WWAuthLogTestCase {

  /**
  * IP to use for curl requests.
  * @var string
  **/
  const IP_ADDR = '1.2.3.4';

  /**
  * Trigger multiple failed login tests.
  **/
  public function testFailedLogin() {
    for ($i=0; $i <= 5; $i++) {
      parent::testFailedLogin();
      sleep(1);// avoid blocking from mod_evasive
    }
  }

  /**
  * @inheritdoc
  **/
  protected function curlExec($curl_options, $redirect = FALSE) {
    // spoof ip address
    $curl_options[CURLOPT_INTERFACE] = self::IP_ADDR;
    parent::curlExec($curl_options, $redirect);
  }
}
