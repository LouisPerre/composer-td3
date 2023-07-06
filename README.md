[![Minimum PHP Version](https://img.shields.io/badge/%3E%3D%208.0-black?logo=php&label=PHP)](https://packagist.org/packages/louisperre/composer-td3)
## Information
Easily scrap the TMDB website

## Features
- Scrap all the movies categories name
- Scrap all the movies categories slug
- Scrap all the movies related to a category
- Scrap all the data of a movie

**Work without any api key**

- [Installation](#-installation-)
- [Usage](#-usage-)
- [How does that work](#-how-does-that-work--)
- [Local development](#-local-development-)
- [License](#-license-)

# 🔥 Installation 🔥
```
composer require louisperre/composer-td3
```

# ⚙️ Usage ⚙️
```php
<?php

use louisperr/ApiTmdb;

$api = new ApiTmdb();
$categories = $api->getNameCategories() // array
```

## ✨ How does that work ? ✨
I use the **HttpClient** from **Symfony** to get the HTTP Code of TMDB :
```php
$client = HttpClient::create();
$response = $client->request(
    'GET',
    'url'    
);
$content = $response->getContent();
```
After that I use the **Crawler** to find and loop over the content and get what I want :
```php
$crawler = new Crawler($content);
$list = $crawler
    ->filter('CSS SELECTOR')
    ->reduce(function (Crawler $node, $i) use (&$array) : bool {
        foreach ($node->filter('selector') as $something) {
            // Do something
        }
    })
```
The `filter` function allow me to navigate inside the **HTML CODE** and the `reduce` one act as a callback function to do some logic to the result and the result I want.

## 🔧 Local development 🔧

```bash
# Install the dependencies
composer install
```

```bash
# Test the type of all the project
php vendor/bin/phpstan analyze src --level=max
```

```bash
# Execute all the test
php vendor/bin/phpunit --testdox tests
```


# 📝 License 📝

Licensed under the terms of the MIT License.