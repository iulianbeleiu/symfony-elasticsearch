### Demo ElasticSearch in Symfony

#### Installation
````
> docker-compose up
````

#### Run fixtures
````
> php bin/console doctrine:fixtures:load
````
#### Create Elastic Search Index
````
> php bin/console app:elasticsearch:create-index
````

#### How to test
````
http://localhost/blog?q=Ipsum
````