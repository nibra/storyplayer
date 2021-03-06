<?php

/**
 * Copyright (c) 2011-present Mediasift Ltd
 * Copyright (c) 2015-present Ganbaro Digital Ltd
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
 * @package   Storyplayer/DefinitionLib
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\DefinitionLib;

use Storyplayer\SPv3\TestEnvironments\HostAdapter;
use Storyplayer\SPv3\TestEnvironments\HostAdapterValidator;
use Storyplayer\SPv3\TestEnvironments\OsAdapter;
use Storyplayer\SPv3\TestEnvironments\OsAdapterValidator;

use DataSift\Storyplayer\DefinitionLib\TestEnvironment_RolesValidator;

use DataSift\Stone\ObjectLib\BaseObject;

/**
 * Represents a host defined in the TestEnvironment
 *
 * @category  Libraries
 * @package   Storyplayer/DefinitionLib
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class TestEnvironment_HostDefinition
{
    /**
     * the settings that apps may be interested in
     *
     * @var BaseObject
     */
    protected $storySettings;

    public function __construct(TestEnvironment_GroupDefinition $parentGroup, $hostId)
    {
        $this->setParentGroup($parentGroup);
        $this->setHostId($hostId);

        // start with an empty set of story settings
        $this->storySettings = new BaseObject;

        // start with an empty set of provisioning params
        $this->initProvisioningParams();
    }

    // ==================================================================
    //
    // Parent group support
    //
    // ------------------------------------------------------------------

    /**
     * the group that this host belongs to
     *
     * @var TestEnvironment_GroupDefinition
     */
    protected $parentGroup;

    /**
     * which test environment group do we belong to?
     *
     * @return TestEnvironment_GroupDefinition
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    /**
     * tell us which test environment group we belong to
     *
     * @param  TestEnvironment_GroupDefinition $parentGroup
     * @return TestEnvironment_HostDefinition
     */
    public function setParentGroup(TestEnvironment_GroupDefinition $parentGroup)
    {
        $this->parentGroup = $parentGroup;

        // fluent interface
        return $this;
    }

    // ==================================================================
    //
    // hostId support
    //
    // ------------------------------------------------------------------

    /**
     * the ID assigned to this host (it's complicated)
     * @var string
     */
    protected $hostId;

    /**
     * what is the ID of this host?
     *
     * host IDs are the names that Storyplayer (and stories you write) will
     * use to refer to a host. They may or may not be the same as the host's
     * hostname.
     *
     * @return string
     */
    public function getHostId()
    {
        return $this->hostId;
    }

    /**
     * tell this host what its host ID is
     *
     * NOTES:
     *
     * * host IDs are normally validated in the group definition
     *
     * @param string $hostId
     *        the host ID to use for this host
     */
    public function setHostId($hostId)
    {
        $this->hostId = $hostId;
    }

    // ==================================================================
    //
    // Operating system support
    //
    // ------------------------------------------------------------------

    /**
     * plugin that we use to talk to the operating system running
     * inside this host
     *
     * @var OsAdapter
     */
    protected $osAdapter;

    /**
     * what operating system is running on this host?
     *
     * @return OsAdapter
     */
    public function getOperatingSystem()
    {
        return $this->osAdapter;
    }

    /**
     * tell this host what operating system is running on this host
     *
     * @param OsAdapter $osAdapter
     *        the adapter to use for the relevant operating system
     */
    public function setOperatingSystem(OsAdapter $osAdapter)
    {
        // make sure the operating system is compatible with this group

        // remember the adapter
        $this->osAdapter = $osAdapter;

        // fluent interface
        return $this;
    }

    // ==================================================================
    //
    // Host adapter support
    //
    // ------------------------------------------------------------------

    /**
     * the adapter to use when interacting with this host
     *
     * @var HostAdapter
     */
    protected $hostAdapter;

    /**
     * how do we interact with this host?
     *
     * @return HostAdapter
     */
    public function getHostAdapter()
    {
        return $this->hostAdapter;
    }

    /**
     * tell us how to interact with this host
     *
     * @param HostAdapter $hostAdapter
     *        the adapter we should use
     */
    public function setHostAdapter(HostAdapter $hostAdapter)
    {
        // we need to validate first
        $validator = $this->getParentGroup()->getHostAdapterValidator();
        if (!$validator->validate($hostAdapter)) {
            throw new E4xx_IncompatibleHostAdapter(
                $this->getTestEnvironmentName(),
                $this->getGroupId(),
                $this->getHostId(),
                $hostManager,
                $this->getGroupAdapter()
            );
        }

        // remember the adapter
        $this->hostAdapter = $hostAdapter;

        // fluent interface
        return $this;
    }

    // ==================================================================
    //
    // Roles support
    //
    // ------------------------------------------------------------------

    /**
     * a list of the roles that this host supports
     * @var array<string>
     */
    protected $roles = [];

    /**
     * what roles does this host support?
     *
     * @return array<string>
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * tell this host what roles it supports
     *
     * @param array<string> $roles
     *        a list of the roles that it supports
     */
    public function setRoles($roles)
    {
        // make sure we have what we expect
        $rolesValidator = new TestEnvironment_RolesValidator($this);
        $rolesValidator->validate($roles);

        // remember the roles
        $this->roles = $roles;

        // all done
        // fluent interface support
        return $this;
    }

    /**
     * does this host support this role?
     *
     * @param  string $roleName
     *         the role to check for
     * @return boolean
     *         TRUE if the role is supported
     *         FALSE otherwise
     */
    public function hasRole($roleName)
    {
        // special case
        if ($roleName === '*') {
            return true;
        }

        // general case
        if (in_array($roleName, $this->roles)) {
            return true;
        }

        // no luck, daddy-o!
        return false;
    }

    /**
     * add an extra role to our list of supported roles
     *
     * @param string $roleName
     *        the name of the role to add
     * @return void
     */
    public function addRole($roleName)
    {
        // avoid duplicating roles
        if ($this->hasRole($roleName)) {
            return;
        }

        $this->roles[] = $roleName;
    }

    // ==================================================================
    //
    // Settings support goes here
    //
    // ------------------------------------------------------------------

    /**
     * return all of the story settings that this host supports
     *
     * @return BaseObject
     */
    public function getStorySettings()
    {
        return $this->storySettings;
    }

    /**
     * tell this host what story settings it supports
     *
     * @param array|object|null $rawSettings
     *        the settings to set
     */
    public function setStorySettings($rawSettings)
    {
        // just in case we've been called more than once
        $this->storySettings = new BaseObject;

        // convert to our BaseObject, which comes with all sorts of
        // funky helpers
        $this->storySettings->mergeFrom($rawSettings);

        // all done
        // flient interface support
        return $this;
    }

    // ==================================================================
    //
    // Provisioning params support goes here
    //
    // ------------------------------------------------------------------

    /**
     * a list of provisioning parameters to send over to any provisioning
     * engine
     *
     * @var BaseObject
     */
    protected $provisioningParams;

    /**
     * initialise the provisioning params list
     *
     * @return void
     */
    protected function initProvisioningParams()
    {
        $this->provisioningParams = new BaseObject();
    }

    /**
     * what provisioning params do we have?
     *
     * @return BaseObject
     */
    public function getProvisioningParams()
    {
        return $this->provisioningParams;
    }

    /**
     * set our provisioning parameters
     *
     * NOTES:
     *
     * * this will overwrite any existing provisioning parameters that
     *   have already been set
     * * if you pass in an object, we'll copy the details. we don't keep
     *   a handle to your original object
     *
     * @param mixed $rawSettings
     *        the parameters to set
     *
     * @return TestEnvironment_HostDefinition
     */
    public function setProvisioningParams($rawSettings)
    {
        // just in case we've been called more than once
        $this->initProvisioningParams();

        // convert to our BaseObject, which comes with all sorts of
        // funky helpers
        $this->provisioningParams->mergeFrom($rawSettings);

        // all done
        // fluent interface support
        return $this;
    }

    /**
     * add some extra provisioning parameters to any that have already been
     * defined
     *
     * this is mostly used when we merge parameters across from the
     * SystemUnderTest config
     *
     * @param mixed $extraParams
     *        the extra parameters to merge in
     *
     * @return TestEnvironment_HostDefinition
     */
    public function addProvisioningParams($extraParams)
    {
        // let's get them merged in
        $this->provisioningParams->mergeFrom($extraParams);

        // fluent interface support
        return $this;
    }

    // ==================================================================
    //
    // SPv2.0-style config support goes here
    //
    // ------------------------------------------------------------------

    /**
     * generate an SPv2.0-style config block describing this host
     *
     * @return BaseObject
     */
    public function getHostAsConfig()
    {
        // our return value
        $retval = new BaseObject;

        $retval->name   = $this->getHostId();
        $retval->osName = $this->osAdapter->getOsName();
        $retval->roles  = $this->getRoles();
        $retval->storySettings = $this->getStorySettings();
        $retval->params = $this->getProvisioningParams();

        // all done
        return $retval;
    }

    // ==================================================================
    //
    // Helpers go here
    //
    // ------------------------------------------------------------------

    /**
     * get access to the adapter for the group that we belong to
     *
     * @return \Storyplayer\TestEnvironments\GroupAdapter
     */
    public function getGroupAdapter()
    {
        return $this->parentGroup->getGroupAdapter();
    }

    /**
     * what is the ID of the group that we belong to?
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->parentGroup->getGroupId();
    }

    /**
     * what is the name of the test environment that we belong to?
     *
     * @return string
     */
    public function getTestEnvironmentName()
    {
        return $this->parentGroup->getTestEnvironmentName();
    }
}
