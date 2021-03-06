<?php

namespace ailiangkuai\yii2\queue;

use Yii;
use yii\base\Component;
use yii\caching\Dependency;
use yii\helpers\StringHelper;


/**
 * Class Queue
 * @package xsteach\yii\queue
 * @author yaoyongfeng
 */
abstract class Queue extends Component
{

    /**
     * @var string a string prefixed to every cache key so that it is unique globally in the whole cache storage.
     * It is recommended that you set a unique cache key prefix for each application if the same cache
     * storage is being used by different applications.
     *
     * To ensure interoperability, only alphanumeric characters should be used.
     */
    public $keyPrefix;

    /**
     * @var null|array|false the functions used to serialize and unserialize cached data. Defaults to null, meaning
     * using the default PHP `serialize()` and `unserialize()` functions. If you want to use some more efficient
     * serializer (e.g. [igbinary](http://pecl.php.net/package/igbinary)), you may configure this property with
     * a two-element array. The first element specifies the serialization function, and the second the deserialization
     * function. If this property is set false, data will be directly sent to and retrieved from the underlying
     * cache component without any serialization or deserialization. You should not turn off serialization if
     * you are using [[Dependency|cache dependency]], because it relies on data serialization. Also, some
     * implementations of the cache can not correctly save and retrieve data different from a string type.
     */
    public $serializer;

    /**
     * Retrieves a value from cache with a specified key.
     * @param mixed $key a key identifying the cached value. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @return mixed the value stored in cache, false if the value is not in the cache, expired,
     * or the dependency associated with the cached data has changed.
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key);
        if ($value === false || $this->serializer === false) {
            return $value;
        } elseif ($this->serializer === null) {
            $value = unserialize($value);
        } else {
            $value = call_user_func($this->serializer[1], $value);
        }
        return $value;
    }


    /**
     * Stores a value identified by a key into cache.
     * If the cache already contains such a key, the existing value and
     * expiration time will be replaced with the new ones, respectively.
     *
     * @param mixed $key a key identifying the value to be cached. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @param mixed $value the value to be cached
     * @param Dependency $dependency dependency of the cached item. If the dependency changes,
     * the corresponding value in the cache will be invalidated when it is fetched via [[get()]].
     * This parameter is ignored if [[serializer]] is false.
     * @return boolean whether the value is successfully stored into cache
     */
    public function put($key, $value)
    {
        $key = $this->buildKey($key);
        if ($this->serializer === null) {
            $value = serialize($value);
        } elseif ($this->serializer !== false) {
            $value = call_user_func($this->serializer[0], $value);
        }

        return $this->putValue($key, $value);
    }


    /**
     * Builds a normalized cache key from a given key.
     *
     * If the given key is a string containing alphanumeric characters only and no more than 32 characters,
     * then the key will be returned back prefixed with [[keyPrefix]]. Otherwise, a normalized key
     * is generated by serializing the given key, applying MD5 hashing, and prefixing with [[keyPrefix]].
     *
     * @param mixed $key the key to be normalized
     * @return string the generated cache key
     */
    public function buildKey($key)
    {
        if (is_string($key)) {
            $key = ctype_alnum($key) && StringHelper::byteLength($key) <= 32 ? $key : md5($key);
        } else {
            $key = md5(json_encode($key));
        }

        return $this->keyPrefix . $key;
    }

    /**
     * Stores a value identified by a key in cache.
     * This method should be implemented by child classes to store the data
     * in specific cache storage.
     * @param string $key the key identifying the value to be cached
     * @param string $value the value to be cached
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    abstract protected function putValue($key, $value);

    /**
     * Retrieves a value from cache with a specified key.
     * This method should be implemented by child classes to retrieve the data
     * from specific cache storage.
     * @param string $key a unique key identifying the cached value
     * @return mixed|false the value stored in cache, false if the value is not in the cache or expired. Most often
     * value is a string. If you have disabled [[serializer]], it could be something else.
     */
    abstract protected function getValue($key);
}