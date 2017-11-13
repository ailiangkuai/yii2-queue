<?php
/**
 * 亿牛集团
 * 本源代码由亿牛集团及其作者共同所有，未经版权持有者的事先书面授权，
 * 不得使用、复制、修改、合并、发布、分发和/或销售本源代码的副本。
 *
 * @copyright Copyright (c) 2017 yiniu.com all rights reserved.
 */


namespace ailiangkuai\yii2\queue;


use yii\di\Instance;
use yii\redis\Connection;

/**
 * Class RedisQueue
 * @package ailiangkuai\yii2\queue
 * To use redis Queue as the cache application component, configure the application as follows,
 *
 * ~~~
 * [
 *     'components' => [
 *         'queue' => [
 *             'class' => 'ailiangkuai\yii2\queue\RedisQueue',
 *             'redis' => [
 *                 'hostname' => 'localhost',
 *                 'port' => 6379,
 *                 'database' => 0,
 *             ]
 *         ],
 *     ],
 * ]
 * ~~~
 *
 * Or if you have configured the redis [[Connection]] as an application component, the following is sufficient:
 *
 * ~~~
 * [
 *     'components' => [
 *         'cache' => [
 *             'class' => 'ailiangkuai\yii2\queue\RedisQueue',
 *             // 'redis' => 'redis' // id of the connection application component
 *         ],
 *     ],
 * ]
 * ~~~
 *
 * @author yaoyongfeng
 */
class RedisQueue extends Queue
{
    /**
     * @var Connection|string|array the Redis [[Connection]] object or the application component ID of the Redis [[Connection]].
     * This can also be an array that is used to create a redis [[Connection]] instance in case you do not want do configure
     * redis connection as an application component.
     * After the Cache object is created, if you want to change this property, you should only assign it
     * with a Redis [[Connection]] object.
     */
    public $redis = 'redis';

    /**
     * Initializes the redis Cache component.
     * This method will initialize the [[redis]] property to make sure it refers to a valid redis connection.
     * @throws \yii\base\InvalidConfigException if [[redis]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->redis = Instance::ensure($this->redis, Connection::className());
    }

    public function putValue($key, $value)
    {
        return (bool)$this->redis->executeCommand('LPUSH', [$key, $value]);
    }

    public function getValue($key)
    {
        return $this->redis->executeCommand('RPOP', [$key]);
    }
}