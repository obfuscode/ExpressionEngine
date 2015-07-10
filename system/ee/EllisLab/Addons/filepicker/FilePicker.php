<?php

namespace EllisLab\Addons\FilePicker;


use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Library\Data\Collection;
use EllisLab\ExpressionEngine\Model\File\UploadDestination;

class FilePicker {

	public $base_url = 'addons/settings/filepicker/';
	public $controller = 'addons/settings/filepicker/modal';

	public function inject($view)
	{
		// Insert the modal
		$modal_vars = array('name'=> 'modal-file', 'contents' => '');
		$modal = ee('View')->make('_shared/modal')->ee_view('_shared/modal', $modal_vars, TRUE);

		if (empty($view->blocks['modals']))
		{
			$view->blocks['modals'] = '';
		}

		if (strpos($view->blocks['modals'], $modal) === FALSE) {
			$view->blocks['modals'] .= $modal;
		}

		ee()->cp->add_js_script(array(
			'file' => array(
				'cp/files/picker'
			),
		));
	}

	public function link($text, $dir = 'all', $data = array())
	{
		$href = ee('CP/URL', $this->controller, array('directory' => $dir));
		$extra = "";

		if ( ! empty($data['image']))
		{
			$extra .= " data-input-image='{$data['image']}'";
		}

		if ( ! empty($data['input']))
		{
			$extra .= " data-input-value='{$data['input']}'";
		}

		if ( ! empty($data['name']))
		{
			$extra .= " data-input-name='{$data['name']}'";
		}

		if ( ! empty($data['callback']))
		{
			$extra .= " data-callback='{$data['callback']}'";
		}

		return "<a class='m-link filepicker' rel='modal-file' href='$href' $extra>". $text ."</a>";
	}

	public function buildTableFromFileCollection(Collection $files, $limit = 20)
	{
		$table = new Table(array('autosort' => TRUE, 'limit' => $limit));
		$table->setColumns(
			array(
				'title_or_name' => array(
					'encode' => FALSE
				),
				'file_type',
				'date_added',
				'manage' => array(
					'type'	=> Table::COL_TOOLBAR
				),
				array(
					'type'	=> Table::COL_CHECKBOX
				)
			)
		);
		$table->setNoResultsText(lang('no_uploaded_files'));

		$data = array();

		$file_id = ee()->session->flashdata('file_id');

		foreach ($files as $file)
		{
			if ( ! $file->getUploadDestination()
				|| $this->hasFileGroupAccessPrivileges($file->getUploadDestination()) === FALSE
				|| ! $file->exists())
			{
				continue;
			}

			$toolbar = array(
				'edit' => array(
					'href' => ee('CP/URL', 'files/file/edit/' . $file->file_id),
					'title' => lang('edit')
				),
				'crop' => array(
					'href' => ee('CP/URL', 'files/file/crop/' . $file->file_id),
					'title' => lang('crop'),
				),
				'download' => array(
					'href' => ee('CP/URL', 'files/file/download/' . $file->file_id),
					'title' => lang('download'),
				),
			);

			if ( ! $file->isImage())
			{
				unset($toolbar['crop']);
			}

			$column = array(
				$file->title . '<br><em class="faded">' . $file->file_name . '</em>',
				$file->mime_type,
				ee()->localize->human_time($file->upload_date),
				array('toolbar_items' => $toolbar),
				array(
					'name' => 'selection[]',
					'value' => $file->file_id,
					'data' => array(
						'confirm' => lang('file') . ': <b>' . htmlentities($file->title, ENT_QUOTES) . '</b>'
					)
				)
			);

			$attrs = array();

			if ($file_id && $file->file_id == $file_id)
			{
				$attrs = array('class' => 'selected');
			}

			$data[] = array(
				'attrs'		=> $attrs,
				'columns'	=> $column
			);
		}

		$table->setData($data);

		return $table;
	}

	public function hasFileGroupAccessPrivileges(UploadDestination $dir)
	{
		// 2 = Banned
		// 3 = Guests
		// 4 = Pending
		$hardcoded_disallowed_groups = array('2', '3', '4');

		$member_group_id = ee()->session->userdata['group_id'];
		// If the user is a Super Admin, return true
		if ($member_group_id == 1)
		{
			return TRUE;
		}

		if (in_array($member_group_id, $hardcoded_disallowed_groups))
		{
			return FALSE;
		}

		if ( ! $dir)
		{
			return FALSE;
		}

		if (in_array($member_group_id, $dir->getNoAccess()->pluck('group_id')))
		{
			return FALSE;
		}

		return TRUE;
	}

}

?>
