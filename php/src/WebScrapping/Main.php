<?php

namespace Chuva\Php\WebScrapping;

use DOMXPath;

/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new \DOMDocument('1.0', 'utf-8');
    $dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $data = (new Scrapper())->scrap($dom);

    // Write your logic to save the output file bellow.
    print_r($data);
  }
  private static function scrapVolumeInfo(\DOMDocument $dom) {

    $xpath = new DOMXPath($dom);
    $elements = $xpath->query('//div[@class="volume-info"]');
  }

}
