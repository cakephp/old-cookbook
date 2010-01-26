<?php
/**
 * Short description for node.test.php
 *
 * Long description for node.test.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       cookbook
 * @subpackage    cookbook.tests.cases.models
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Model', 'Node');

/**
 * NodeTestCase class
 *
 * @uses          CakeTestCase
 * @package       cookbook
 * @subpackage    cookbook.tests.cases.models
 */
class NodeTestCase extends CakeTestCase {

/**
 * Node property
 *
 * @var mixed null
 * @access public
 */
	var $Node = null;

/**
 * fixtures property
 *
 * @var array
 * @access public
 */

	var $fixtures = array(
		'app.node',
		'app.revision',
		'plugin.users.user',
		'plugin.users.group',
		'plugin.users.level',
		'plugin.users.profile',
		'app.comment'
	);

	function startTest() {
		$this->Node = ClassRegistry::init('Node');
	}

	function endTest() {
		unset($this->Node);
		ClassRegistry::flush();
	}

	function testAddToTree() {

	}

/**
 * testFind method
 *
 * Initial test to ensure the fixture is loaded correctly, and find('list' is working correctly
 *
 * @return void
 * @access public
 */
	function testFind() {
		$this->Node->recursive = -1;
		$expected = array(
			1 => ' Your Collections',
			' Collection 1',
			' Book 1',
			'1 Section id 4',
			'1.1 Section id 5',
			'1.1.1 Section id 6',
			'1.1.2 Section id 7',
			'1.2 Section id 8',
			'1.2.1 Section id 9',
			'1.2.2 Section id 10',
			'2 Section id 11',
			'2.1 Section id 12',
			'2.1.1 Section id 13',
			'2.1.2 Section id 14',
			'2.2 Section id 15',
			'2.2.1 Section id 16',
			'2.2.2 Section id 17',
			' Book 2',
			'1 Section id 19',
			'1.1 Section id 20',
			'1.1.1 Section id 21',
			'1.1.2 Section id 22',
			'1.2 Section id 23',
			'1.2.1 Section id 24',
			'1.2.2 Section id 25',
			'2 Section id 26',
			'2.1 Section id 27',
			'2.1.1 Section id 28',
			'2.1.2 Section id 29',
			'2.2 Section id 30',
			'2.2.1 Section id 31',
			'2.2.2 Section id 32',
			' Collection 2',
			' Book 1',
			'1 Section id 35',
			'1.1 Section id 36',
			'1.1.1 Section id 37',
			'1.1.2 Section id 38',
			'1.2 Section id 39',
			'1.2.1 Section id 40',
			'1.2.2 Section id 41',
			'2 Section id 42',
			'2.1 Section id 43',
			'2.1.1 Section id 44',
			'2.1.2 Section id 45',
			'2.2 Section id 46',
			'2.2.1 Section id 47',
			'2.2.2 Section id 48',
			' Book 2',
			'1 Section id 50',
			'1.1 Section id 51',
			'1.1.1 Section id 52',
			'1.1.2 Section id 53',
			'1.2 Section id 54',
			'1.2.1 Section id 55',
			'1.2.2 Section id 56',
			'2 Section id 57',
			'2.1 Section id 58',
			'2.1.1 Section id 59',
			'2.1.2 Section id 60',
			'2.2 Section id 61',
			'2.2.1 Section id 62',
			'2.2.2 Section id 63'
		);
		$results = $this->Node->find('list');
		$this->assertEqual($results, $expected);
	}

	function testFindNeighbor() {

	}

	function testBook() {

	}

	function testCopy() {
		$this->Node->recursive = -1;
		$expected = array(
			1 => ' Your Collections',
			' Collection 1',
			' Book 1',
			'1 Section id 4',
			'1.1 Section id 5',
			'1.1.1 Section id 6',
			'1.1.2 Section id 7',
			'1.2 Section id 8',
			'1.2.1 Section id 9',
			'1.2.2 Section id 10',
			'2 Section id 11',
			'2.1 Section id 12',
			'2.1.1 Section id 13',
			'2.1.2 Section id 14',
			'2.2 Section id 15',
			'2.2.1 Section id 16',
			'2.2.2 Section id 17',
			' Book 2',
			'1 Section id 19',
			'1.1 Section id 20',
			'1.1.1 Section id 21',
			'1.1.2 Section id 22',
			'1.2 Section id 23',
			'1.2.1 Section id 24',
			'1.2.2 Section id 25',
			'2 Section id 26',
			'2.1 Section id 27',
			'2.1.1 Section id 28',
			'2.1.2 Section id 29',
			'2.2 Section id 30',
			'2.2.1 Section id 31',
			'2.2.2 Section id 32',
			' Collection 2',
			' Book 1',
			'1 Section id 35',
			'1.1 Section id 36',
			'1.1.1 Section id 37',
			'1.1.2 Section id 38',
			'1.2 Section id 39',
			'1.2.1 Section id 40',
			'1.2.2 Section id 41',
			'2 Section id 42',
			'2.1 Section id 43',
			'2.1.1 Section id 44',
			'2.1.2 Section id 45',
			'2.2 Section id 46',
			'2.2.1 Section id 47',
			'2.2.2 Section id 48',
			' Book 2',
			'1 Section id 50',
			'1.1 Section id 51',
			'1.1.1 Section id 52',
			'1.1.2 Section id 53',
			'1.2 Section id 54',
			'1.2.1 Section id 55',
			'1.2.2 Section id 56',
			'2 Section id 57',
			'2.1 Section id 58',
			'2.1.1 Section id 59',
			'2.1.2 Section id 60',
			'2.2 Section id 61',
			'2.2.1 Section id 62',
			'2.2.2 Section id 63',
			/* Copied */
			' Collection 1',
			' Book 1',
			'1 Section id 4',
			'1.1 Section id 5',
			'1.1.1 Section id 6',
			'1.1.2 Section id 7',
			'1.2 Section id 8',
			'1.2.1 Section id 9',
			'1.2.2 Section id 10',
			'2 Section id 11',
			'2.1 Section id 12',
			'2.1.1 Section id 13',
			'2.1.2 Section id 14',
			'2.2 Section id 15',
			'2.2.1 Section id 16',
			'2.2.2 Section id 17',
			' Book 2',
			'1 Section id 19',
			'1.1 Section id 20',
			'1.1.1 Section id 21',
			'1.1.2 Section id 22',
			'1.2 Section id 23',
			'1.2.1 Section id 24',
			'1.2.2 Section id 25',
			'2 Section id 26',
			'2.1 Section id 27',
			'2.1.1 Section id 28',
			'2.1.2 Section id 29',
			'2.2 Section id 30',
			'2.2.1 Section id 31',
			'2.2.2 Section id 32',
		);
		$id = $this->Node->Revision->field('node_id', array('title' => 'Collection 1'));
		$this->Node->copy($id);
		$results = $this->Node->find('list');
		$this->assertEqual($results, $expected);
	}

	function testCollection() {

	}

	function testExportDatum() {

	}

	function testGeneratetreelist() {

	}

	function testImport() {

	}

	function testInitialize() {

	}

	function testMerge() {

	}

	function testMoveUp() {

	}

	function testMoveDown() {

	}

	function testReset() {

	}

	function testResetDepth() {

	}

	function testResetSequence() {

	}

	function testSetLanguage() {

	}
}