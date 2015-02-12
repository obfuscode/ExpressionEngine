<?php

namespace EllisLab\ExpressionEngine\Service\Validation\Rule;

use EllisLab\ExpressionEngine\Library\Filesystem\Filesystem;
use EllisLab\ExpressionEngine\Service\Validation\ValidationRule;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine File Exists Validation Rule
 *
 * @package		ExpressionEngine
 * @subpackage	Validation\Rule
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class FileExists extends ValidationRule {

	protected $fs;

	public function validate($value)
	{
		return $this->getFilesystem()->exists($path);
	}

	public function getLanguageKey()
	{
		return 'file_exists';
	}

	protected function getFilesystem()
	{
		if ( ! isset($this->fs))
		{
			$this->fs = new Filesystem();
		}

		return $this->fs;
	}

}
