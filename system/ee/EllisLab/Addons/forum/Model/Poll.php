<?php

namespace EllisLab\Addons\Forum\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Poll Model for the Forum
 *
 * A model representing a poll in the Forum.
 *
 * @package		ExpressionEngine
 * @subpackage	Forum Module
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Poll extends Model {

	protected static $_primary_key = 'poll_id';
	protected static $_table_name = 'forum_polls';

	protected static $_typed_columns = array(
		'topic_id'      => 'int',
		'author_id'     => 'int',
		'poll_date'     => 'timestamp',
		'total_votes'   => 'int',
	);

	// protected static $_relationships = array(
	// );

	protected static $_validation_rules = array(
		'topic_id'      => 'required',
		'poll_question' => 'required',
		'poll_answers'  => 'required',
		'poll_date'     => 'required',
	);

	protected $poll_id;
	protected $topic_id;
	protected $author_id;
	protected $poll_question;
	protected $poll_answers;
	protected $poll_date;
	protected $total_votes;

}