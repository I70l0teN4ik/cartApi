# Simple application allowing adding products to the cart #


## Initialization ##
```bash
$ php bin/console doctrine:migrations:migrate
```
```bash
$ php bin/console api:generate:products
```


## Product API ##

### Add a new product ###
`[POST] /product`
The request should have the following body:
```
{
    "name": "Fallout",
    "price": 1.23
}
```
The API will respond with the newly created product info:
```
{
    "id": 1,
    "name": "Fallout",
    "price": 1.23,
    "created": "2017-02-12T19:06:55+0100"
}
```

### Remove product ###
`[DELETE] /product/{id}`
This method silently remove product if such product does exist


### Update product title and/or price ###
`[PUT] /product/{id}`
The request should have the following body:
```
{
    "price": 1.99
}
```
The API will respond with the updated created product info:
```
{
    "id": 1,
    "name": "Fallout",
    "price": 1.99,
    "created": "2017-02-12T19:06:55+0100"
}
```

### Get single product info ###
`[GET] /product/{id}`
The API will respond with the updated created product info:
```
{
    "id": 1,
    "name": "Fallout",
    "price": 1.99,
    "created": "2017-02-12T19:06:55+0100"
}
```

### Get products list ###
`[GET] /products`
The API will respond with sorted and paginated products list:
```
{
  "products": [
    {
      "id": 1,
      "name": "Fallout",
      "price": 1.99,
      "created": "2017-02-12T19:06:55+0100"
    },
    {
      "id": 2,
      "name": "Don’t Starve",
      "price": 2.99,
      "created": "2017-02-12T19:06:56+0100"
    },
    {
      "id": 3,
      "name": "Baldur’s Gate",
      "price": 3.99,
      "created": "2017-02-12T19:06:57+0100"
    }
  ],
  "pagination": {
    "next": "http://base.store.api/products?page=2"
  }
}
```
This method accepts one of the sorting params: `name`, `price` or `created`.
To sort products list by price in descending order just request: `[GET] /products?sort=-price`

## Cart API ##

### Create a cart ###
`[POST] /cart`
The request body could be empty or include existing Product id witch will be added to new cart immediately:
```
{
    "product": 1
}
```
The API will respond with the newly created cart info:
```
{
  "id": 1,
  "products": [
    {
      "id": 1,
      "name": "Fallout",
      "price": 1.99,
      "created": "2017-02-12T19:06:55+0100"
    }
  ],
  "created": "2017-02-13T12:13:50+0100",
  "total_price": 1.99
}
```

### Add or Remove a product to the cart ###
`[PUT] /cart/{id}`
The request should have the following body:
```
{
    "product": 1,
    "action": "remove"
}
```
This method accepts two types of `action`: `add` or `remove`. The Api will respond with updated cart info:
```
{
  "id": 1,
  "products": [],
  "created": "2017-02-13T12:13:50+0100",
  "total_price": 0
}
```

### List all the products in the cart ###
`[GET] /cart/{id}`
The API will respond with the specific cart info:
```
{
  "id": 1,
  "products": [
    {
      "id": 3,
      "name": "Baldur’s Gate",
      "price": 3.99,
      "created": "2017-02-12T19:06:57+0100"
    },
    {
      "id": 5,
      "name": "Bloodborne",
      "price": 5.99,
      "created": "2017-02-12T19:06:59+0100"
    }
  ],
  "created": "2017-02-12T19:19:54+0100",
  "total_price": 9.98
}
```

### Remove cart ###
`[DELETE] /cart/{id}`
This method silently removes cart


## Error handling ##
In case of error API will response with 4xx Client Error Status and following structure:
```
Status: 409 Conflict
{
  "error": {
    "code": 409,
    "message": "Conflict"
  }
}
```