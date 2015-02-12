---
layout: v2/using-stories
title: Action Phase
prev: '<a href="../../using/stories/pre-test-inspection.html">Prev: Pre-Test Inspection Phase</a>'
next: '<a href="../../using/stories/post-test-inspection.html">Next: Post-Test Inspection Phase</a>'
---

# Action Phase

At the heart of each test are the steps that actually perform the test.  For stories, these steps do exactly what a user would to perform the story.

This phase is **required**.

## Running Order

The `Action()` happens after everything has been setup, and after any prediction or inspection

1. Test Environment Setup
1. Test Setup
1. Pre-test Prediction
1. Pre-test Inspection
1. __Action__
1. Post-test Inspection
1. Test Teardown
1. Test Environment Teardown

## Getting Things Done

You will need to add an `Action()` function to your story:

{% highlight php %}
$story->addAction(function(StoryTeller $st) {
    // steps go here
});
{% endhighlight %}

Your `Action()` will make extensive use of [Storyplayer's modules](../modules/index.html).

Write your `Action()` as if everything has worked.  If something hasn't worked, Storyplayer's modules will throw an exception for Storyplayer to catch.  _You don't need to catch these exceptions yourself._

## Templating Your Actions

Each `Action()` is unique to each story, and cannot be templated.