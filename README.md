# Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-passwordless-login.svg?style=flat-square)](https://packagist.org/packages/opengento/module-module)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-passwordless-login.svg?style=flat-square)](./LICENSE)
[![Packagist](https://img.shields.io/packagist/dt/opengento/module-passwordless-login.svg?style=flat-square)](https://packagist.org/packages/opengento/module-module/stats)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-passwordless-login.svg?style=flat-square)](https://packagist.org/packages/opengento/module-module/stats)

This module allows to you login without a password and without using a third-party service.

- [Setup](#setup)
    - [Composer installation](#composer-installation)
    - [Setup the module](#setup-the-module)
- [Features](#features)
- [Settings](#settings)
- [Documentation](#documentation)
- [Support](#support)
- [Authors](#authors)
- [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

###  Composer installation

Run the following composer command: (Not available on Packagist yet)

```
composer require opengento/module-passwordless-login
```

### Setup the module

Run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Features

### Country to store mapping

Define many countries to many stores relation. This configuration will allows Magento to map stores with countries.

## Settings

The configuration for this module is available in `Stores > Configuration > Customer > PasswordLess Login`.

## Documentation


## Support

Raise a new [request](https://github.com/opengento/magento2-passwordless-login/issues) to the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-module.svg?style=flat-square)](https://github.com/opengento/magento2-module/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
