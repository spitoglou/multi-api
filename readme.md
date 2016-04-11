Laravel 5.2 Multi Api Package

[![Latest Stable Version](https://poser.pugx.org/spitoglou/multi-api/version)](https://packagist.org/packages/spitoglou/multi-api)
[![Total Downloads](https://poser.pugx.org/spitoglou/multi-api/downloads)](https://packagist.org/packages/spitoglou/multi-api)
[![Latest Unstable Version](https://poser.pugx.org/spitoglou/multi-api/v/unstable)](//packagist.org/packages/spitoglou/multi-api)
[![License](https://poser.pugx.org/spitoglou/multi-api/license)](https://packagist.org/packages/spitoglou/multi-api)

# spitoglou/multi-api

This package was initially developed for personal (and colleague) use.

It provides the necessary functionality to produce api responses in JSON, XML or HTML (simple table) formats. 
The api consumer determines the format of the response he/she needs by setting the "Accept" header of the request accordingly 
("application/json", "application/custom+xml","application/text" respectively).

## Install

Via Composer

``` bash
$ composer require spitoglou/multi-api
```

## Usage
#### Normal Response

``` php
$array = [["name"=>"Stavros", "surname"=>"Pitoglou],["name"=>"John", "surname"=>"Doe"];
$sender = new Spitoglou\MultiApi\Sender($array);
$sender->finalSend();
```
#### Error
``` php
$array = ["errorCode"=>"654987", "errorDescription"=>"Some Exotic Error]
$sender = new Spitoglou\MultiApi\Sender($array);
$sender->sendError(500);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

Have Not Added Tests yet...

## Security

If you discover any security related issues, please email s.pitoglou@csl.gr instead of using the issue tracker.

## Credits

- [Stavros Pitoglou][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
