<?php

class KeywordExtractor {
  private $keywords, $keywordActualDuplicates, $stopWords, $titleKeywords;

  /**
   * set default values of attributes and perpare stop keywords array
   */
  public function __construct() {
    $this->keywords = $this->keywordActualDuplicates = $this->stopWords = $this->titleKeywords = array();
    $this->prepareStopWords();
  }

  /**
   * display top keywords in certail files
   *
   * @param integer $topCount
   */
  public function displayTopKeywords($topCount) {
    $top_keywords = $this->getTopKeywords($topCount);
    echo '<meta charset="UTF-8">';
    echo '<center>';
    echo '<h2>Top 10 Keywords : </h2>';
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>Keyword</th>';
    echo '<th>Weight</th>';
    echo '<th>Actual Duplicates</th>';
    echo '<th>Exists in title?</th>';
    echo '</tr>';
    foreach($top_keywords as $keyword => $duplicates) {
      $actualDuplicates = $this->getActualDuplicates($keyword);
      $exists = ($actualDuplicates != $duplicates) ? 'yes' : 'no';
      echo '<tr>';
      echo '<td>'. $keyword .'</td>';
      echo '<td>'. $duplicates .'</td>';
      echo '<td>'. $actualDuplicates .'</td>';
      echo '<td>'. $exists .'</td>';
      echo '</tr>';
    }
    echo '</table>';
    echo '</center>';
  }

  /**
   * it set stopWords attribute to hold all stop words
   */
  private function prepareStopWords() {
    $file_name = 'stopwords.txt';
    $content = file_get_contents($file_name);
    $stopwords = $this->getTextKeywords($content);
    $this->stopWords = $stopwords;
  }

  /**
   * it retrieve all keywords and sort it according to duplications
   */
  private function retreiveKeywords() {
    // get file names that match that regex
    $file_names = glob("post*.md");

    foreach($file_names as $file_name) {
      // get content of file
      $content = file_get_contents($file_name);

      // get keywords of that content
      $keywords = $this->getTextKeywords($content);

      // merge keywords attribute with new keywords
      $this->keywords = array_merge($this->keywords, $keywords);

      // set keywords found in titles
      $this->setTitleKeywords($content);
    }

    // eleminate stop words
    $this->keywords = array_diff($keywords, $this->stopWords);

    // count duplicates
    $this->keywords = array_count_values($this->keywords);

    // set some extra attributes to keywords
    $this->setKeywordAtrributes();

    // sorting keywords by value
    arsort($this->keywords);
  }

  /**
   * copy actual duplicates of the keywords to obtain us to double keywords found in titles
   *
   * @param string $content
   */
  private function setKeywordAtrributes( $content ) {
    $this->titleKeywords = array_count_values($this->titleKeywords);
    foreach( $this->titleKeywords as $titleKeyword => $duplicates ) {
      if(isset($this->keywords[$titleKeyword])) {
        $actualDuplicates = $this->keywords[$titleKeyword];
        $this->keywordActualDuplicates[$titleKeyword] = $actualDuplicates;
        $this->keywords[$titleKeyword] = $actualDuplicates  + $duplicates;
      }
    }
  }

  /**
   * it returns array of keywords for a certain content
   *
   * @param string $content
   * @return array $keywords
   */
  private function getTextKeywords($content) {
    $content = mb_strtolower( $content, "utf-8" );
    mb_regex_encoding( "utf-8" );
    $keywords = mb_split( '[\s,]+', $content );
    foreach( $keywords as $index => $keyword ) {
      $keywords[$index] = trim($keyword);
      if( $keywords[$index] == '' ) {
        unset($keywords[$index]);
      }
    }

    return $keywords;
  }

  /**
   * set title keywords for each file
   *
   * @param string $content
   */
  private function setTitleKeywords($content) {
    preg_match_all('/^[#]+(.*)$/m', $content, $matches);
    if(!empty($matches)) {
      $matches = $matches[1];
      foreach( $matches as $match ) {
        $this->titleKeywords = array_merge($this->titleKeywords, $this->getTextKeywords($match));
      }
    }
  }

  /**
   * get top keywords customized by count
   *
   * @param integer $topCount
   * @return array $topKeywords
   */
  private function getTopKeywords($topCount) {
    $this->retreiveKeywords();
    $topKeywords = array_slice($this->keywords, 0, $topCount);

    return $topKeywords;
  }

  /**
   * calculate actual duplicate of each keyword
   *
   * @param string @keyword
   * @return integer @actualDuplicates
   */
  private function getActualDuplicates($keyword) {
    $actualDuplicates = 0;
    if(isset($this->keywordActualDuplicates[$keyword] )) {
      $actualDuplicates = $this->keywordActualDuplicates[$keyword];
    }
    else {
      $actualDuplicates = $this->keywords[$keyword];
    }

    return $actualDuplicates;
  }

}

$extractor = new KeywordExtractor;
$extractor->displayTopKeywords(10);
