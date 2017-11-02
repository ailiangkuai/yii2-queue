<?php
/**
 * 亿牛集团
 * 本源代码由亿牛集团及其作者共同所有，未经版权持有者的事先书面授权，
 * 不得使用、复制、修改、合并、发布、分发和/或销售本源代码的副本。
 *
 * @copyright Copyright (c) 2017 yiniu.com all rights reserved.
 */


namespace ailiangkuai\yii2\queue;


use yii\base\NotSupportedException;

class HttpSqsQueue extends Queue
{
    public function putValue($key, $value)
    {
        throw new NotSupportedException('暂时不支持');
    }

    public function getValue($key)
    {
        throw new NotSupportedException('暂时不支持');
    }
}