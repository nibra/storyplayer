<?php

/**
 * Copyright (c) 2011-present Mediasift Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Storyplayer/OsLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\OsLib;

use DataSift\Storyplayer\CommandLib\SshClient;

/**
 * adds support for running commands via ssh
 *
 * @category  Libraries
 * @package   Storyplayer/OsLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

trait Connector_SshClient
{
    // keep track of sshClients so that we can reuse them
    private $sshClients = [];

    /**
     * get an SSH client to contact this host
     *
     * @param  \DataSift\Storyplayer\PlayerLib\StoryTeller $st
     *         our ubiquitous state object
     * @param  \DataSift\Storyplayer\HostLib\HostDetails $hostDetails
     *         details about the host that you want a client for
     * @return \DataSift\Storyplayer\CommandLib\SshClient
     *         the SSH client for you to use
     */
    public function getClient($st, $hostDetails)
    {
        // shorthand
        $hostId = $hostDetails->hostId;

        // do we already have a client?
        if (isset($this->sshClients[$hostId])) {
            // yes - reuse it
            return $this->sshClients[$hostId];
        }

        // if we get here, we need to make a new client
        $sshClient = new SshClient($st, $hostDetails->sshOptions, $hostDetails->scpOptions);
        $sshClient->setIpAddress($hostDetails->ipAddress);
        $sshClient->setSshUsername($hostDetails->sshUsername);

        if (isset($hostDetails->sshKey)) {
            $sshClient->setSshKey($hostDetails->sshKey);
        }

        // all done
        $this->sshClients[$hostId] = $sshClient;
        return $sshClient;
    }
}