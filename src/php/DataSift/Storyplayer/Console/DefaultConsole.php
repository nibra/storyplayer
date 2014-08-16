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
 * @package   Storyplayer/Console
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\Console;

use DataSift\Storyplayer\OutputLib\CodeFormatter;
use DataSift\Storyplayer\Phases\Phase;
use DataSift\Storyplayer\PlayerLib\Phase_Result;
use DataSift\Storyplayer\PlayerLib\PhaseGroup_Result;
use DataSift\Storyplayer\PlayerLib\Story_Result;
use DataSift\Storyplayer\PlayerLib\Story;
use DataSift\Stone\LogLib\Log;

/**
 * the console plugin we use unless the user specifies something else
 *
 * @category  Libraries
 * @package   Storyplayer/Console
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class DefaultConsole extends Console
{
	protected $currentPhase;
	protected $phaseNumber = 0;
	protected $phaseMessages = array();
	protected $verbosityLevel = 0;
	protected $logLevelStrings = [
        Log::LOG_EMERGENCY => "EMERGENCY ",
        Log::LOG_ALERT     => "ALERT     ",
        Log::LOG_CRITICAL  => "CRITICAL  ",
        Log::LOG_ERROR     => "ERROR     ",
        Log::LOG_WARNING   => "WARNING   ",
        Log::LOG_NOTICE    => "NOTICE    ",
        Log::LOG_INFO      => "INFO      ",
        Log::LOG_DEBUG     => "DEBUG     ",
        Log::LOG_TRACE     => "TRACE     ",
	];

	/**
	 * a list of the results we have received from stories
	 * @var array
	 */
	protected $storyResults = [];

	/**
	 * are we running totally silently?
	 * @var boolean
	 */
	protected $silentActivity = false;

	public function setVerbosity($verbosityLevel)
	{
		$this->verbosityLevel = $verbosityLevel;
		if ($this->verbosityLevel > 2) {
			$this->verbosityLevel = 2;
		}
	}

	public function resetSilent()
	{
		$this->silentActivity = false;
	}

	public function setSilent()
	{
		$this->silentActivity = true;
	}

	/**
	 * called when storyplayer starts
	 *
	 * @param string $version
	 * @param string $url
	 * @param string $copyright
	 * @param string $license
	 * @return void
	 */
	public function startStoryplayer($version, $url, $copyright, $license)
	{
		$this->write("Storyplayer {$version}", $this->writer->highlightStyle);
		$this->write(" - ");
		$this->write($url, $this->writer->urlStyle);
		$this->write(PHP_EOL);
		$this->write($copyright . PHP_EOL);
		$this->write($license . PHP_EOL . PHP_EOL);
	}

	/**
	 * called when Storyplayer exits
	 *
	 * @return void
	 */
	public function endStoryplayer()
	{
		return;

		// this is our opportunity to tell the user how our story(ies)
		// went in detail
		foreach ($this->storyResults as $storyResult)
		{
			// do we need to say anything more?
			switch ($storyResult->resultCode)
			{
				case Story_Result::PASS:
				case Story_Result::BLACKLISTED:
					// no, we're happy enough
					break;

				default:
					// everything else is an error of some kind

					// sanity check: we should always have a failedPhase
					if (!$storyResult->failedPhase instanceof Phase_Result) {
						throw new E5xx_MissingFailedPhase();
					}
					$this->showActivityForPhase($storyResult->story, $storyResult->failedPhase);
					break;
			}
		}
	}

	/**
	 * called when we start a new set of phases
	 *
	 * @param  string $name
	 * @return void
	 */
	public function startPhaseGroup($name)
	{
		if ($this->verbosityLevel > 0) {
			$output = <<<EOS
=============================================================

{$name}

-------------------------------------------------------------

EOS;
			$this->write($output);
		}
		else {
			$this->write($name . ': ');
		}
	}

	public function endPhaseGroup($name, PhaseGroup_Result $result)
	{
		$this->write(' [' . $result->getResultString() . '] (' . round($result->getDuration(), 2) . ' secs)' . PHP_EOL);
	}

	/**
	 * called when a new story starts
	 *
	 * a single copy of Storyplayer may execute multiple tests
	 *
	 * @param string $storyName
	 * @param string $storyCategory
	 * @param string $storyGroup
	 * @param string $envName
	 * @param string $deviceName
	 * @return void
	 */
	public function startStory($storyName, $storyCategory, $storyGroup, $envName, $deviceName)
	{
		if ($this->verbosityLevel > 0) {
			$output = <<<EOS
=============================================================

      Story: {$storyName}
   Category: {$storyCategory}
      Group: {$storyGroup}

Environment: {$envName}
     Device: {$deviceName}

-------------------------------------------------------------


EOS;
		}
		else {
			$output = $storyName . ': ';
		}
		$this->write($output);


		// reset the phaseNumber counter
		$this->phaseNumber = 0;
	}

	/**
	 * called when a story finishes
	 *
	 * @param Story_Result $storyResult
	 * @return void
	 */
	public function endStory(Story_Result $storyResult)
	{
		// var_dump($storyResult);

		$this->write(' [' . $storyResult->getResultString() . ']'
		     . ' (' . round($storyResult->getDuration(), 2) . ' secs)'
		     . PHP_EOL);

		// add this story result to our collection
		$this->storyResults[] = $storyResult;
	}

	/**
	 * called when a story starts a new phase
	 *
	 * @param string $phaseName
	 * @param integer $phaseType
	 * @return void
	 */
	public function startPhase($phaseName, $phaseType)
	{
		// make sure we can keep track of what the phase is doing
		$this->phaseMessages[$phaseName] = [];
		$this->currentPhase = $phaseName;

		// we're only interested in telling the user about the
		// phases of a story
		if ($phaseType !== Phase::STORY_PHASE) {
			return;
		}

		// increment our internal counter
		$this->phaseNumber++;

		// tell the user which phase we're doing
		if ($this->verbosityLevel > 0) {
			$this->write($phaseName . ': ');
		}
		else {
			$this->write($this->phaseNumber);
		}
	}

	/**
	 * called when a story ends a phase
	 *
	 * @param string $phaseName
	 * @param integer $phaseType
	 * @return void
	 */
	public function endPhase($phaseName, $phaseType)
	{
		// we're only interested in telling the user about the
		// phases of a story
		if ($phaseType !== Phase::STORY_PHASE) {
			return;
		}

		if ($this->verbosityLevel > 0) {
			$this->write(PHP_EOL);
		}
		else {
			$this->write(' ');
		}
	}

	/**
	 * called when a story logs an action
	 *
	 * @param integer $level
	 * @param string $msg
	 * @return void
	 */
	public function logPhaseActivity($level, $msg)
	{
		// keep track of what was attempted, in case we need to show
		// the user what was attempted
		$this->phaseMessages[$this->currentPhase][] = [
			'ts'    => time(),
			'level' => $level,
			'text'  => $msg
		];

		// show the user that *something* happened
		if (!$this->silentActivity) {
			$this->write(".");
		}
	}

	/**
	 * called when a story logs an error
	 *
	 * @param string $phaseName
	 * @param string $msg
	 * @return void
	 */
	public function logPhaseError($phaseName, $msg)
	{
		// we have to show this now, and save it for final output later
		$this->write("e");

		$this->phaseMessages[$this->currentPhase][] = [
			'ts'    => time(),
			'level' => Log::LOG_CRITICAL,
			'text'  => $msg
		];
	}

	/**
	 * called when a story is skipped
	 *
	 * @param string $phaseName
	 * @param string $msg
	 * @return void
	 */
	public function logPhaseSkipped($phaseName, $msg)
	{
		// we have to show this now, and save it for final output later
		$this->write("s");

		// $this->phaseMessages[$phaseName] = $msg;
	}

	/**
	 * called when the outer CLI shell encounters a fatal error
	 *
	 * @param  string $msg
	 *         the error message to show the user
	 *
	 * @return void
	 */
	public function logCliError($msg)
	{
		$this->write("*** error: $msg" . PHP_EOL);
	}

	/**
	 *
	 * @param  string $msg
	 * @param  Exception $e
	 * @return void
	 */
	public function logCliErrorWithException($msg, $e)
	{
		$this->write("*** error: $msg" . PHP_EOL . PHP_EOL
		     . "This was caused by an unexpected exception " . get_class($e) . PHP_EOL . PHP_EOL
		     . $e->getTraceAsString());
	}

	/**
	 * called when the outer CLI shell needs to publish a warning
	 *
	 * @param  string $msg
	 *         the warning message to show the user
	 *
	 * @return void
	 */
	public function logCliWarning($msg)
	{
		$this->write("*** warning: $msg" . PHP_EOL);
	}

	/**
	 * called when the outer CLI shell needs to tell the user something
	 *
	 * @param  string $msg
	 *         the message to show the user
	 *
	 * @return void
	 */
	public function logCliInfo($msg)
	{
		$this->write($msg . PHP_EOL);
	}

	/**
	 * an alternative to using PHP's built-in var_dump()
	 *
	 * @param  string $name
	 *         a human-readable name to describe $var
	 *
	 * @param  mixed $var
	 *         the variable to dump
	 *
	 * @return void
	 */
	public function logVardump($name, $var)
	{
		// this is a no-op for us
	}

	/**
	 * called when we start to create a test environment
	 *
	 * @param  string $testEnvName
	 * @return void
	 */
	public function startTestEnvironmentCreation($testEnvName)
	{
		if ($this->verbosityLevel > 0) {
			$output = <<<EOS
=============================================================

Creating Test Environment: {$testEnvName}

EOS;
		}
		else {
			$output = "Creating test environment {$testEnvName}: ";
		}
		$this->write($output);
	}

	/**
	 * called when we have finished making the test environment
	 *
	 * @param  string $testEnvName
	 * @return void
	 */
	public function endTestEnvironmentCreation($testEnvName)
	{
		$this->write(PHP_EOL);
	}

	/**
	 * called when we start to destroy a test environment
	 *
	 * @param  string $testEnvName
	 * @return void
	 */
	public function startTestEnvironmentDestruction($testEnvName)
	{
		if ($this->verbosityLevel > 0) {
			$output = <<<EOS
=============================================================

Destroying Test Environment: {$testEnvName}

EOS;
		}
		else {
			$output = "Destroying test environment {$testEnvName}: ";
		}
		$this->write($output);
	}

	/**
	 * called when we have finished destroying a test environment
	 *
	 * @param  string $testEnvName
	 * @return void
	 */
	public function endTestEnvironmentDestruction($testEnvName)
	{
		$this->write(PHP_EOL);
	}
}
