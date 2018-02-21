<?php

namespace SV\ViewStickyThreads;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function upgrade2000070Step1()
	{
	    $this->db()->query("delete from xf_permission_entry where permission_group_id = 'forum' and permission_id = 'viewStaff'");
	}

    public function uninstallStep1()
    {
        $this->db()->query("delete from xf_permission_entry where permission_group_id = 'forum' and permission_id = 'viewStaff'");
        $this->db()->query("delete from xf_permission_entry where permission_group_id = 'forum' and permission_id = 'viewStickies'");
    }
}
