# Elasticsearch

````
PUT app_index
PUT app_index/_mapping/articles
{
  "properties": {
    "id": {
      "type": "integer"
  	},
    "title": {
  	  "type": "string"
    },
	  "description": {
  	  "type": "text"
  	},  
	 "content": {
  	  "type": "text"
  	 },	
    "categories": {
      "type": "nested"
    },
    "ingredients": {
      "type": "nested"
    },
    "tags": {
      "type": "nested"
    },
    "products": {
      "type": "nested"
    }
  }
}
````
