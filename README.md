# yii2-random-provider

#### ActiveDataProvider with random selection ####

[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-random-provider/v/stable)](https://packagist.org/packages/sjaakp/yii2-random-provider)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-random-provider/downloads)](https://packagist.org/packages/sjaakp/yii2-random-provider)
[![License](https://poser.pugx.org/sjaakp/yii2-random-provider/license)](https://packagist.org/packages/sjaakp/yii2-random-provider)

**RandomProvider** is derived from [**ActiveDataProvider**](https://www.yiiframework.com/doc/api/2.0/yii-data-activedataprovider)
of the [Yii 2.0](https://www.yiiframework.com/ "Yii") PHP Framework. It selects the records in a random
fashion, which in some cases may be more attractive than the orderly way a regular
**ActiveDataProvider** (usually) does it. **RandomProvider** is intended to co-operate with my
[**LoadMorePager**](https://sjaakpriester.nl/software/loadmore), but it will work with
[**LinkPager**](https://www.yiiframework.com/doc/api/2.0/yii-widgets-linkpager) or other pagers as well.

Notice that **RandomProvider** doesn't support `CUBRID` or `dblib` database drivers. Moreover, 
I only tested it with `mysql`. I'm pretty sure it'll work with other drivers, though.
If you  have any experiences to share, I'll appreciate that.

Notice also that **RandomProvider** makes use of an algorithm named '*Order By Rand()*'. This is
rather slow, and doesn't scale very well. Therefore, it is advised to use **RandomProvider** only with
relatively small data sets (think of less than a few thousands of records).
More information [here](https://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/).
 
A demonstration of **RandomProvider** is [here](http://www.sjaakpriester.nl/software/randomprovider).

## Installation ##

Install **yii2-random-provider** in the usual way with [Composer](https://getcomposer.org/). 
Add the following to the `require` section of your `composer.json` file:

`"sjaakp/yii2-random-provider": "*"` 

or run:

`composer require sjaakp/yii2-random-provider` 

You can manually install **yii2-random-provider** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-random-provider/archive/master.zip).

## Using RandomProvider ##

**RandomProvider** is a drop-in replacement for Yii's
[**ActiveDataProvider**](https://www.yiiframework.com/doc/api/2.0/yii-data-activedataprovider).
Just use it like **ActiveDataProvider**.
