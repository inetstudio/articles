# Elasticsearch

````
curl -X PUT "localhost:9200/app_index_articles" -H 'Content-Type: application/json' -d'
{
   "mappings":{
      "properties":{
          "type": {
             "type":"keyword"
          },            
          "id":{
             "type":"integer"
          },
          "is_published": {
             "type":"boolean"
          },
          "classifiers": {
          	"type":"integer"
          },            
          "title":{
             "type":"text"
          },
          "description":{
             "type":"text"
          },
          "content":{
             "type":"text"
          },
          "publish_date": {
             "type":"date"
          },
          "tags":{
             "type":"nested"
          },
          "products":{
             "type":"nested"
          },
          "ingredients":{
             "type":"nested"
          }            
       } 
   }
}
'
````
