<?php

/*
 * This file is part of the Chisimba framework.
 *
 * @author Paul Scott <pscott209@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists($file = __DIR__.'/../vendor/.composer/autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../vendor/.composer/autoload.php.dist')) {
    require_once $file;
}

if (file_exists($file = __DIR__.'/../src/app.php')) {
    require_once $file;
}