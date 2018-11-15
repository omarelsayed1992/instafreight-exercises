## instafreight-exercises


#### Ex. 01 - Route decode :

* Its implementation will be found in Directions.php.

#### Ex. 02 - r/UpliftingNews consumer :
  - Its implementation will be found in UpliftingNews.php.
  - **What further improvements would you implement?**
      - We can use OOP to represent news as a class that will hold its attributes.
      - We can use pagination later.
  - **What can we do in order to reduce requests?**
      - We can use any caching system , we can cache it in DB,files or any data store.
#### Ex. 03 - Keyword extractor
  - Its implementation will be found in KeywordExtractor.php.
  - **It support exclude words found in stopwords.txt file.**
  - **Keywords found in titles are doubled to increase its weight.**
  - **German text is also supported :) .**
  - **Informative table is displayed to check keywords duplicates count and if it's found in title.**
  - **Some improvements could be applied later like:-**
    - Use stemming to convert verbs and other keywords to its original word.
    - Improve stopwords.txt file to get more not used words , and enhance it fix some issues like words that are written with apostrophe two shapes (') & (’).
