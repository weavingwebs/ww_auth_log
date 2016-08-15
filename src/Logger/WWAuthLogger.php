<?php
namespace Drupal\ww_auth_log\Logger;

use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Psr\Log\LoggerInterface;

class WWAuthLogger implements LoggerInterface {
  use RfcLoggerTrait;

  /**
   * Constructs a WWAuthLogger object.
   *
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(LogMessageParserInterface $parser) {
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    // ignore non user messages
    if ($context['channel'] !== 'user') {
      return;
    }

    global $base_url;

    // Populate the message placeholders and then replace them in the message.
    $message_placeholders = $this->parser->parseMessagePlaceholders($message, $context);
    $message = empty($message_placeholders) ? $message : strtr($message, $message_placeholders);

    // format message
    $message = strtr('!date !host !base_url|!timestamp|!type|!ip|!request_uri|!referer|!uid|!link|!message', array(
      '!date' => date('M j H:i:s Y', $context['timestamp']),
      '!host' => gethostname(),
      '!base_url'    => $base_url,
      '!timestamp'   => $context['timestamp'],
      '!type'        => $context['channel'],
      '!ip'          => $context['ip'],
      '!request_uri' => $context['request_uri'],
      '!referer'     => $context['referer'],
      '!uid'         => $context['uid'],
      '!link'        => strip_tags($context['link']),
      '!message'     => strip_tags($message),
    ));

    // write to drupal_auth.log
    error_log($message."\n", 3, DRUPAL_ROOT.'/../logs/drupal_auth.log');
  }

}
