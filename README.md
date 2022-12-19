# Foodics Exercise

#### Clone and change directory

```
git clone git@github.com:khaled-dev/exercise.git

cd exercise
```

#### Copy `env` file
```
cp .env.example .env
```
###


#### Build docker image and run the containers
```
docker-compose up 
```
###
> If port 80 already taken, try to change the env variable `APP_PORT` and rerun the previous command
###

#### Run database migrations and seed
```
docker exec -it foodics_app php artisan migrate

docker exec -it foodics_app php artisan db:seed
```

#### To re-migrate the database
```
docker exec -it foodics_app php artisan migrate:fresh
```

#### For mailing dashboard `MailHog` check the following URI
```
http://localhost:8025/
```

###
> If port 8025 already taken, try to change the env variable `FORWARD_MAILHOG_DASHBOARD_PORT` and restart the containers
###

#### Run the tests
```
docker exec -it foodics_app php artisan test
```

#

----

###

# API End-Points

### End-point: {domain}/products
#### Method: GET
```
{{domain}}/api/products
```
#### Headers

|Content-Type|Value|
|---|---|
|Accept|application/json|
|Content-Type|application/json|



⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃

### End-point: {domain}/orders
#### Method: GET
```
{{domain}}/api/orders
```
#### Headers

|Content-Type|Value|
|---|---|
|Accept|application/json|
|Content-Type|application/json|



⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃

### End-point: {domain}/orders
#### Method: POST
```
{{domain}}/api/orders
```
#### Headers

|Content-Type|Value|
|---|---|
|Accept|application/json|
|Content-Type|application/json|


### Body (**raw**)

```json
{
    "products": [
        {"product_id": 2, "quantity": 4},
        {"product_id": 1, "quantity": 2}
    ]
}
```

