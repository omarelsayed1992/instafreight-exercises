<?php

  class News {
    // it's holding feed url
    private $feeds_url;

    /**
     * @param string $feeds_url
     */
    public function __construct($feeds_url) {
      $this->feeds_url = $feeds_url;
    }

    /**
     * @param string $url
     * @return stdclass $response
     *
     */
    public function curlQuery($url) {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);

      $json = curl_exec($ch);

      curl_close($ch);

      $response = json_decode($json);

      return $response;
    }

    /**
     * @return array $news
     */
    private function retreive() {
      $page = $this->curlQuery($this->feeds_url);
      $news = $page->data->children;

      return $news;
    }

    /**
     * print news titles
     */
    public function display() {

      $newsList = $this->retreive();

      foreach( $newsList as $news ) {
        echo '<h3>' . $news->data->title . '</h3>';
      }
    }
}

$news = new News('https://www.reddit.com/r/UpliftingNews/.json');
$news->display();
