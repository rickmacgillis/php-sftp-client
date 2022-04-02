# PHP SFTP Client

Originally forked from `nicolab/php-ftp-client`, this project now supports Windows FTP and SSH FTP connections by mashing it with `phpseclib/phpseclib`.

A flexible FTP and SSH-FTP client for PHP.
This lib provides helpers easy to use to manage the remote files.

> This package is aimed to remain simple and light. It's only a wrapper of the FTP native API of PHP, with some useful helpers. If you want to customize some methods, you can do this by inheriting one of the [3 classes of the package](src/FtpClient).


## Install

  * Use composer: _require_ `rickmacgillis/php-ftp-client`

  * Or use GIT clone command: `git clone git@github.com:rickmacgillis/php-ftp-client.git`

  * Or download the library, configure your autoloader or include the 3 files of `php-ftp-client/src/FtpClient` directory.


## Getting Started

Connect to a server FTP :

```php
$ftp = new FtpClient();
$ftp->setHost($host);
$ftp->setPass($pass);
$ftp->setUser($user);
$ftp->setPort($port); // Defaults to port 21 if not explicitly set.
$ftp->setPassiveMode($passiveMode); // Set to true. (Defaults to false/active mode.)
$ftp->setTimeout($timeoutSeconds);
$ftp->setUseSsh(false);

$ftp->connect();
```

OR

Connect to a server FTP via SSH (on port 22 or another port) :

```php
$ftp = new FtpClient();
$ftp->setHost($host);
$ftp->setPass($pass);
$ftp->setUser($user);
$ftp->setPort($port); // Defaults to port 22 if not explicitly set.
$ftp->setTimeout($timeoutSeconds);
$ftp->setUseSsh(true);
```

Note: The connection is implicitly closed at the end of script execution (when the object is destroyed). Therefore it is unnecessary to call `$ftp->close()`, except for an explicit re-connection.

## License

All code is released under the [MIT license](https://github.com/rickmacgillis/php-ftp-client/blob/master/LICENSE).

## Credits
1. PHPSecLib team for building the SFTP integration components.
2. Nicolas Talle (nicolab) for the original FTP client code and inspiration for extending his package.
3. Rick Mac Gillis (rickmacgillis) for merging the two codebases and providing a unified interface for easy use no matter which type of authentication you need for FTP.

## TODO
1. Add better test coverage.
2. Add more documentation
