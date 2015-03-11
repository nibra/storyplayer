---
layout: v2/modules-host
title: fromHost()
prev: '<a href="../../modules/host/supported-hosts.html">Prev: Supported Hosts</a>'
next: '<a href="../../modules/host/expectsHost.html">Next: expectsHost()</a>'
---

# fromHost()

_fromHost()_ allows you to get information about host and its current state.

The source code for these actions can be found in the class _DataSift\Storyplayer\Prose\FromHost_.

## Behaviour And Return Codes

Every action returns either a value on success, or `NULL` on failure.  These actions do throw an exception if you attempt to work with an unknown host.

## getDetails()

Use `fromHost()->getDetails()` to retrieve the host's entry in Storyplayer's [hosts table](../hoststable/how-hosts-are-remembered.html).

{% highlight php startinline %}
$details = fromHost($hostName)->getDetails();
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$details` is a PHP object containing the host's entry in the hosts table

__NOTE__

* `$details` isn't a clone of the hosts table entry; any changes you make to these details will be persistent

## getHostIsRunning()

Use `fromHost()->getHostIsRunning()` to determine if the specified host is currently running or not.

{% highlight php startinline %}
$isRunning = fromHost($hostName)->getHostIsRunning();
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$isRunning` is _TRUE_ if the host is currently running, or _FALSE_ otherwise

If the host is not running, this could be because your test has stopped the host or powered it off.  If your test has destroyed the host, then calling this action will throw an exception.

## getInstalledPackageDetails()

Use `fromHost()->getInstalledPackageDetails()` to get information about an installed package from the guest operating system's inventory.

{% highlight php startinline %}
$details = fromHost($hostName)->getInstalledPackageDetails($packageName);
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$packageName` is the name of the package that you want details about
* `$details` is a PHP object containing information about the package

__NOTE__

* The contents of `$details` are currently operating-system specific.
* If the package is not installed, _isset($details->version)_ will always be _FALSE_.

## getIpAddress()

Use `fromHost()->getIpAddress()` to get the host's current IP address.

{% highlight php startinline %}
$ipAddress = fromHost($hostName)->getIpAddress();
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$ipAddress` is the IP address of an active network interface

__NOTE__

* If the virtual machine has multiple active network interfaces, only one will be returned.  This is an area which may require more work in a future release of Storyplayer.

## getPid()

Use `fromHost()->getPid()` to get the process ID of a running process.

{% highlight php startinline %}
$pid = fromHost($hostName)->getPid($processName);
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$processName` is the string to search the output of `ps` for
* `$pid` is the process ID of the process that you searched for, or `NULL` if the process is not running

__NOTE__

* If multiple processes match `$processName`, only one process ID will be returned.  This is an area which may require more work in a future release of Storyplayer.

## getProcessIsRunning()

Use `fromHost()->getProcessIsRunning()` to determine if a process is currently running or not.

{% highlight php startinline %}
$isRunning = fromHost($hostName)->getProcessIsRunning($processName);
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$processName` is the string to search the output of `ps` for
* `$isRunning` is _TRUE_ if the process is running, or _FALSE_ if the process is not running

## getSshUsername()

Use `fromHost()->getSshUsername()` to get the default username used for SSH'ing into the host.

{% highlight php startinline %}
$sshUsername = fromHost($hostName)->getSshUsername();
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$sshUsername` is the default SSH username for that host

## getSshKeyFile()

Use `fromHost()->getSshKeyFile()` to get the path to the SSH private key file that Storyplayer will use in _[usingHost()->runCommand()](usingHost.html#runcommand)_ et al.

{% highlight php startinline %}
$sshKeyFile = fromHost($hostName)->getSshKeyFile();
{% endhighlight %}

where:

* `$hostName` is the name you set when you created the host
* `$sshKeyFile` is the default SSH key file for that host

The SSH private key file is set when the host is originally created (e.g. when _[usingVagrant()->createVm()](../vagrant/usingVagrant.html#createvm)_ is called).