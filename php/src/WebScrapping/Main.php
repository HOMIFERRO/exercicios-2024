<?php

namespace Chuva\Php\WebScrapping;

use DOMDocument;
use DOMXPath;

/**
 * Runner for the Webscrapping exercice.
 */
class Main
{

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void
  {
    $dom = new \DOMDocument('1.0', 'utf-8');
    $dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $data = (new Scrapper())->scrap($dom);

    // Write your logic to save the output file bellow.
    print_r($data);
    self::scrapVolumeInfo($dom);
    self::scrapTitles($dom);
    self::scrapTags($dom);
    self::scrapAuthorsAndInstitutions($dom);
  }
  private static function scrapVolumeInfo(\DOMDocument $dom): array
  {

    $xpath = new DOMXPath($dom);
    $elements = $xpath->query('//div[@class="volume-info"]');

    $volumeInfo = [];
    foreach ($elements as $element) {
      echo $element->nodeValue . PHP_EOL;
      $volumeInfo[] = [
        'volumeInfo' => $element->nodeValue
      ];
    }
    return $volumeInfo;
  }
  private static function scrapTitles(\DOMDocument $dom): array
  {

    $xpath = new DOMXPath($dom);
    $elements = $xpath->query('//h4[@class="my-xs paper-title"]');

    $titles[] = [];
    foreach ($elements as $element) {
      echo $element->nodeValue . PHP_EOL;
      $titles[] = [
        'titles' => $element->nodeValue
      ];
    }
    return $titles;
  }
  private static function scrapTags(\DOMDocument $dom): array {

    $xpath = new DOMXPath($dom);
    $elements= $xpath->query('//div[@class="tags mr-sm"]');

    $tags[] = [];
    foreach ($elements as $element) {
      echo $element->nodeValue. PHP_EOL;
      $tags[] = [
        'tags' => $element->nodeValue
      ];
    }
    return $tags;
  }
  private static function scrapAuthorsAndInstitutions(\DOMDocument $dom) {

    $xpath = new DOMXPath($dom);
    $elements = $xpath->query("//a[@class='paper-card p-lg bd-gradient-left']");

    foreach ($elements as $element) {
      $authors =$xpath->query(".//div[@class='authors']/span", $element);
      $authorsNames = [];
      foreach ($authors as $author) {
        echo $author->nodeValue. PHP_EOL;
        $authorsNames[] = [
          'authors' => $author->nodeValue
        ];
      }

      $institutions = $xpath->query(".//div[@class='authors']/span/@title", $element);
      $institutionsNames = [];
      foreach ($institutions as $institution) {
        echo $institution->nodeValue. PHP_EOL;
      }
    }
  }
}
