<?php

namespace SV\ViewStaffThreads\XF\Entity;

class Thread extends XFCP_Thread
{
	public function canView(&$error = null)
	{
		$canView = parent::canView($error);

		if ($canView)
		{
			return $canView;
		}

		$visitor = \XF::visitor();
		$nodeId = $this->node_id;

		if ($visitor->hasNodePermission($nodeId, 'viewStickies') && $this->sticky)
		{
			return true;
		}

		if ($visitor->hasNodePermission($nodeId, 'viewStaff') && $this->User->is_staff)
		{
			return true;
		}

		return false;
	}
}