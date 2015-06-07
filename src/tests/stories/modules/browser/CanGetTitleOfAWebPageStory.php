<?php

// ========================================================================
//
// STORY DETAILS
//
// ------------------------------------------------------------------------

$story = newStoryFor("Storyplayer")
         ->inGroup(["Modules", "Browser"])
         ->called("Can get title of a web page");

$story->requiresStoryplayerVersion(2);

// ========================================================================
//
// STORY SETUP / TEAR-DOWN
//
// ------------------------------------------------------------------------

// ========================================================================
//
// PRE-TEST PREDICTION
//
// ------------------------------------------------------------------------

// ========================================================================
//
// PRE-TEST INSPECTION
//
// ------------------------------------------------------------------------

// ========================================================================
//
// POSSIBLE ACTION(S)
//
// ------------------------------------------------------------------------

$story->addAction(function() {
	$checkpoint = getCheckpoint();

	usingBrowser()->gotoPage("http://news.bbc.co.uk");
	$checkpoint->title = fromBrowser()->getTitle();
});

// ========================================================================
//
// POST-TEST INSPECTION
//
// ------------------------------------------------------------------------

$story->addPostTestInspection(function() {
	$checkpoint = getCheckpoint();

	// what title are we expecting?
	$expectedTitle = fromStoryplayer()->getStorySetting("modules.http.remotePage.title");

	// do we have the title we expected?
	assertsObject($checkpoint)->hasAttribute('title');
	assertsString($checkpoint->title)->equals($expectedTitle);
});