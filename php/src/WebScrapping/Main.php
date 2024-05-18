<?php

namespace Chuva\Php\WebScrapping;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
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
    $volumeInfo = self::scrapVolumeInfo($dom);
    $titles = self::scrapTitles($dom);
    $tags= self::scrapTags($dom);
    $authorAndInst = self::scrapAuthorsAndInstitutions($dom);
    self::writeToExcel($volumeInfo, $titles, $tags, $authorAndInst);
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
  private static function scrapTags(\DOMDocument $dom): array
  {

    $xpath = new DOMXPath($dom);
    $elements = $xpath->query('//div[@class="tags mr-sm"]');

    $tags[] = [];
    foreach ($elements as $element) {
      echo $element->nodeValue . PHP_EOL;
      $tags[] = [
        'tags' => $element->nodeValue
      ];
    }
    return $tags;
  }
  private static function scrapAuthorsAndInstitutions(\DOMDocument $dom): array
  {

    $xpath = new DOMXPath($dom);
    $elements = $xpath->query("//a[@class='paper-card p-lg bd-gradient-left']");

    foreach ($elements as $element) {
      $authors = $xpath->query(".//div[@class='authors']/span", $element);
      $authorsNames = [];
      foreach ($authors as $author) {
        echo $author->nodeValue . PHP_EOL;
        $authorsNames[] = [
          'authors' => $author->nodeValue
        ];
      }

      $institutions = $xpath->query(".//div[@class='authors']/span/@title", $element);
      $institutionsNames = [];
      foreach ($institutions as $institution) {
        echo $institution->nodeValue . PHP_EOL;
        $institutionsNames[] = [
          'institutions' => $institution->nodeValue
        ];
      }
      $authorAndInst[] = [$authorsNames, $institutionsNames];
    }
    return $authorAndInst;
  }
  private static function writeToExcel(array $volumeInfo, array $titles, array $tags, $authorAndInst): void
  {

    $filePath = __DIR__ . '/../../src/planilha.xlsx';
    $writer = WriterEntityFactory::createXLSXWriter();

    $writer->openToFile($filePath);

    $headRow = [
      'ID', 'Title', 'Type', 'Author1', 'Institution 1'
    ];
    $writer->addRow(WriterEntityFactory::createRowFromArray($headRow));

    $rowCount = max(count($volumeInfo), count($titles), count($tags), count($authorAndInst));

    for ($i = 0; $i < $rowCount; $i++) {
      $rowData = [];

      $rowData[] = isset($volumeInfo[$i]) ? $volumeInfo[$i]['volumeInfo'] : '';
      $rowData[] = isset($titles[$i]) ? $titles[$i]['titles'] : '';
      $rowData[] = isset($tags[$i]) ? $tags[$i]['tags'] : '';

      $authors = isset($authorAndInst[$i][0]) ? $authorAndInst[$i][0] : [];
      $inst = isset($authorAndInst[$i][1]) ? $authorAndInst[$i][1] : [];

      for ($j = 0; $j < 16; $j++) {
        $author = isset($authors[$j]['authors']) ? $authors[$j]['authors'] : '';
        $rowData[] = $author;
      }
      for ($k = 0; $k < 16; $k++) {
        $institution = isset($inst[$k]['institutions']) ? $inst[$k]['institutions'] : '';
        $rowData[] = $institution;
      }

      $writer->addRow(WriterEntityFactory::createRowFromArray($rowData));
    }

    $writer->close();
  }
}
