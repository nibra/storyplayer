<?php

use Storyplayer\SPv3\Modules\Asserts;
use Storyplayer\SPv3\Modules\Checkpoint;
use Storyplayer\SPv3\Stories\BuildStory;

// ========================================================================
//
// STORY DETAILS
//
// ------------------------------------------------------------------------

$story = BuildStory::newStory();

// ========================================================================
//
// STORY SETUP / TEAR-DOWN
//
// ------------------------------------------------------------------------

// ========================================================================
//
// POSSIBLE ACTION(S)
//
// ------------------------------------------------------------------------

$story->addAction(function() {
	$checkpoint = Checkpoint::getCheckpoint();

	// these should pass
	$stringData = "hello, Storyplayer";
	Asserts::assertsString($stringData)->endsWith("player");

	$stringData = "filename.json";
	Asserts::assertsString($stringData)->endsWith(".json");

	// and these should fail
	try {
		$stringData = "filename.json";
		Asserts::assertsString($stringData)->endsWith("name");
	}
	catch (Exception $e) {
		$checkpoint->test1Passed = true;
	}

	try {
		$stringData = "filename.json";
		Asserts::assertsString($stringData)->endsWith(".jso");
	}
	catch (Exception $e) {
		$checkpoint->test2Passed = true;
	}

	try {
		$stringData = "filename.json";
		Asserts::assertsString($stringData)->endsWith("file");
	}
	catch (Exception $e) {
		$checkpoint->test3Passed = true;
	}
});

// ========================================================================
//
// POST-TEST INSPECTION
//
// ------------------------------------------------------------------------

$story->addPostTestInspection(function() {
	$checkpoint = Checkpoint::getCheckpoint();

	for ($i = 1; $i <= 3; $i++) {
		$attribute="test{$i}Passed";
		Asserts::assertsObject($checkpoint)->hasAttributeWithValue($attribute, true);
	}
});