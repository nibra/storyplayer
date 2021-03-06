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
	$actualData = 2.0;
	$expectedData1 = 3;
	Asserts::assertsDouble($actualData)->isLessThanOrEqualTo($expectedData1);

	$actualData = 2.0;
	$expectedData2 = 2;
	Asserts::assertsDouble($actualData)->isLessThanOrEqualTo($expectedData2);

	// and these should fail
	try {
		$expectedData3 = 1;
		Asserts::assertsDouble($actualData)->isLessThanOrEqualTo($expectedData3);
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

	Asserts::assertsObject($checkpoint)->hasAttribute("test3Passed");
	Asserts::assertsBoolean($checkpoint->test3Passed)->isTrue();
});