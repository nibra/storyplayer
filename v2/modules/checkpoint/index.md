---
layout: v2/modules-checkpoint
title: The Checkpoint Module
prev: '<a href="../../modules/browser/webdriver.html">Prev: The WebDriver Library</a>'
next: '<a href="../../modules/checkpoint/getCheckpoint.html">Next: getCheckpoint()</a>'
---

# The Checkpoint Module

## Introduction

The __Checkpoint__ module allows you to work with [Storyplayer's inter-phase checkpoint object](../../using/stories/the-checkpoint.html).

This module is here for convenience; you can achieve the same results using a mixture of plain PHP and the [Assertions module](../assertions/index.html).

The source code for this Prose module can be found in these PHP classes:

* `Prose\FromCheckpoint`
* `Prose\UsingCheckpoint`

## Dependencies

This module has no dependencies.

## Using The Checkpoint Module

### Most Common Way

The most common way to use the checkpoint module is to simply retrieve the Checkpoint object:

{% highlight php startinline %}
$checkpoint = getCheckpoint();
{% endhighlight %}

You can then treat it as an ordinary PHP object by getting and setting attributes.

### Alternative Way

The basic format of an action is:

{% highlight php startinline %}
MODULE()->ACTION();
{% endhighlight %}

where __module__ is one of:

* _[fromCheckpoint()](fromCheckpoint.html)_ - get data from the checkpoint
* _[usingCheckpoint()](usingCheckpoint.html)_ - put data into the checkpoint

and __action__ is one of the methods available on the __module__ you choose.

Here are some examples:

{% highlight php startinline %}
$balance = fromCheckpoint()->get('balance');
{% endhighlight %}

{% highlight php startinline %}
usingCheckpoint()->set('balance', 100);
{% endhighlight %}