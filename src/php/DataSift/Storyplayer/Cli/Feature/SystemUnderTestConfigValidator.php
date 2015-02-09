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
 * @package   Storyplayer/Cli
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\Cli;

use Phix_Project\ValidationLib4\Validator;
use Phix_Project\ValidationLib4\ValidationResult;

class Feature_SystemUnderTestConfigValidator implements Validator
{
    const MSG_NOTVALIDSUT = "Unknown system-under-test '%value%'";

    /**
     * @var DataSift\Storyplayer\ConfigLib\SystemsUnderTestList
     */
    protected $sutList;

    /**
     * @var string
     */
    protected $defaultValue;

    /**
     * @param DataSift\Storyplayer\ConfigLib\SystemsUnderTestList $sutList
     * @param string $defaultValue
     */
    public function __construct($sutList, $defaultValue)
    {
        $this->sutList = $sutList;
        $this->defaultValue = $defaultValue;
    }

    /**
     *
     * @param  mixed $value
     * @param  ValidationResult $result
     * @return ValidationResult
     */
    public function validate($value, ValidationResult $result = null)
    {
        if ($result === null) {
            $result = new ValidationResult($value);
        }

        // strip off .json if it is there
        $value = basename($value, ".json");

        // the $value must be a valid system-under-test name
        if (!$this->sutList->hasEntry($value)) {
            $result->addError(static::MSG_NOTVALIDSUT);
            return $result;
        }

        return $result;
    }
}