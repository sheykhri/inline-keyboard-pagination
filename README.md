# Telegram Bot Inline Keyboard Pagination

[![Scrutinizer Code Quality][code-quality-badge]][code-quality]
[![Codecov][code-coverage-badge]][code-coverage]
[![Build Status][build-status-badge]][build-status]

[![Latest Stable Version][latest-version-badge]][github-tgbot-ikp]
[![Total Downloads][total-downloads-badge]][packagist-tgbot-ikp]
[![License][license-badge]][license]

- [Installation](#installation)
    - [Composer](#composer)
- [Usage](#usage)
    - [Test Data](#test-data)
    - [How To Use](#how-to-use)
    - [Result](#result)
- [Code Quality](#code-quality)
- [License](#license)

## Installation

### Composer
```bash
composer require php-telegram-bot/inline-keyboard-pagination:^1.0.0
```

## Usage

### Test Data
```php
$items = range(1, 100);
$command = 'testCommand'; // optional. Default: pagination
$selectedPage = 10;       // optional. Default: 1
```

### How To Use
```php
$ikp = new InlineKeyboardPagination($items, $command);
$ikp->setMaxButtons(6);
$ikp->setWrapSelectedButton('< #VALUE# >');
    
$pagination = $ikp->pagination($selectedPage); //$ikp->setSelectedPage($selectedPage);
```

### Result
```php
if (!empty($paginate['keyboard'])) {
    $paginate['keyboard'][0]['callback_data']; // testCommand?currentPage=10&nextPage=1
    $paginate['keyboard'][1]['callback_data']; // testCommand?currentPage=10&nextPage=9
    ...

    $response = [
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                $paginate['keyboard'],
            ],
        ]),
    ];
}
```

## Code Quality

Run the PHPUnit tests via Composer script. 

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File][license] for more information.

Project based on [Telegram Bot Pagination][github-lartie-tbp] by [lartie][github-lartie].


[github-tgbot-ikp]: https://github.com/php-telegram-bot/inline-keyboard-pagination "PHP Telegram Bot Inline Keyboard Pagination on GitHub"
[packagist-tgbot-ikp]: https://packagist.org/packages/php-telegram-bot/inline-keyboard-pagination "PHP Telegram Bot Inline Keyboard Pagination on Packagist"
[license]: https://github.com/php-telegram-bot/inline-keyboard-pagination/blob/master/LICENSE "PHP Telegram Bot Inline Keyboard Pagination license"

[code-quality-badge]: https://img.shields.io/scrutinizer/g/php-telegram-bot/inline-keyboard-pagination.svg
[code-quality]: https://scrutinizer-ci.com/g/php-telegram-bot/inline-keyboard-pagination/?branch=master "Code quality on Scrutinizer"
[code-coverage-badge]: https://img.shields.io/codecov/c/github/php-telegram-bot/inline-keyboard-pagination.svg
[code-coverage]: https://codecov.io/gh/php-telegram-bot/inline-keyboard-pagination "Code coverage on Codecov"
[build-status-badge]: https://img.shields.io/travis/php-telegram-bot/inline-keyboard-pagination.svg
[build-status]: https://travis-ci.org/php-telegram-bot/inline-keyboard-pagination "Build status on Travis-CI"

[latest-version-badge]: https://img.shields.io/packagist/v/php-telegram-bot/inline-keyboard-pagination.svg
[total-downloads-badge]: https://img.shields.io/packagist/dt/php-telegram-bot/inline-keyboard-pagination.svg
[license-badge]: https://img.shields.io/packagist/l/php-telegram-bot/inline-keyboard-pagination.svg

[github-lartie-tbp]: https://github.com/lartie/Telegram-Bot-Pagination "Telegram Bot Pagination by Lartie on GitHub"
[github-lartie]: https://github.com/lartie "Lartie on GitHub"
