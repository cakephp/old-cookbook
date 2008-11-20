<?php
/* SVN FILE: $Id: node.test.php 694 2008-11-05 13:59:08Z AD7six $ */
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
 * @version       $Revision: 694 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-05 14:59:08 +0100 (Wed, 05 Nov 2008) $
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
	var $fixtures = array('app.node');
/**
 * start method
 *
 * @return void
 * @access public
 */
	function start() {
		parent::start();
		$this->Node =& ClassRegistry::init('Node');
	}
/**
 * testNodeInstance method
 *
 * @return void
 * @access public
 */
	function testNodeInstance() {
		$this->assertTrue(is_a($this->Node, 'Node'));
	}
/**
 * testNodeFind method
 *
 * Initial test to ensure the fixture is loaded correctly, and find('list' is working correctly
 *
 * @return void
 * @access public
 */
	function testNodeFind() {
		$this->Node->recursive = -1;
		$expected = array(
			1 => ' Your Collections',
			2 => ' Collection 1',
			3 => ' Book 1',
			4 => '1 Section id 4',
			5 => '1.1 Section id 5',
			6 => '1.1.1 Section id 6',
			7 => '1.1.2 Section id 7',
			8 => '1.2 Section id 8',
			9 => '1.2.1 Section id 9',
			10 => '1.2.2 Section id 10',
			11 => '2 Section id 11',
			12 => '2.1 Section id 12',
			13 => '2.1.1 Section id 13',
			14 => '2.1.2 Section id 14',
			15 => '2.2 Section id 15',
			16 => '2.2.1 Section id 16',
			17 => '2.2.2 Section id 17',
			18 => ' Book 2',
			19 => '1 Section id 19',
			20 => '1.1 Section id 20',
			21 => '1.1.1 Section id 21',
			22 => '1.1.2 Section id 22',
			23 => '1.2 Section id 23',
			24 => '1.2.1 Section id 24',
			25 => '1.2.2 Section id 25',
			26 => '2 Section id 26',
			27 => '2.1 Section id 27',
			28 => '2.1.1 Section id 28',
			29 => '2.1.2 Section id 29',
			30 => '2.2 Section id 30',
			31 => '2.2.1 Section id 31',
			32 => '2.2.2 Section id 32',
			33 => ' Collection 2',
			34 => ' Book 1',
			35 => '1 Section id 35',
			36 => '1.1 Section id 36',
			37 => '1.1.1 Section id 37',
			38 => '1.1.2 Section id 38',
			39 => '1.2 Section id 39',
			40 => '1.2.1 Section id 40',
			41 => '1.2.2 Section id 41',
			42 => '2 Section id 42',
			43 => '2.1 Section id 43',
			44 => '2.1.1 Section id 44',
			45 => '2.1.2 Section id 45',
			46 => '2.2 Section id 46',
			47 => '2.2.1 Section id 47',
			48 => '2.2.2 Section id 48',
			49 => ' Book 2',
			50 => '1 Section id 50',
			51 => '1.1 Section id 51',
			52 => '1.1.1 Section id 52',
			53 => '1.1.2 Section id 53',
			54 => '1.2 Section id 54',
			55 => '1.2.1 Section id 55',
			56 => '1.2.2 Section id 56',
			57 => '2 Section id 57',
			58 => '2.1 Section id 58',
			59 => '2.1.1 Section id 59',
			60 => '2.1.2 Section id 60',
			61 => '2.2 Section id 61',
			62 => '2.2.1 Section id 62',
			63 => '2.2.2 Section id 63'
		);
		$results = $this->Node->find('list');
		$this->assertEqual($results, $expected);
	}
}
?>