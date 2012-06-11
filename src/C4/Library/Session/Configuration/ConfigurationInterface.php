<?php 

namespace C4\Library\Session\Configuration;

/**
 * Session configuration
 *
 */
interface ConfigurationInterface
{
    public function setOptions(array $options);
    public function setOption($option, $value);
    public function hasOption($option);
    public function getOption($option);
    public function toArray();

    public function setSavePath($savePath);
    public function getSavePath();

    public function setName($name);
    public function getName();

    public function setCookieLifetime($cookieLifetime);
    public function getCookieLifetime();
    public function setCookiePath($cookiePath);
    public function getCookiePath();
    public function setCookieDomain($cookieDomain);
    public function getCookieDomain();
    public function setCookieSecure($cookieSecure);
    public function getCookieSecure();
    public function setCookieHttpOnly($cookieHTTPOnly);
    public function getCookieHttpOnly();
    public function setUseCookies($useCookies);
    public function getUseCookies();
    public function setRememberMeSeconds($rememberMeSeconds);
    public function getRememberMeSeconds();
}