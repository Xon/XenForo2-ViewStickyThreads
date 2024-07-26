<?php

namespace SV\ViewStickyThreads;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
    use InstallerHelper;
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function upgrade2000070Step1(): void
	{
        $this->executeUpgradeQuery("delete from xf_permission_entry where permission_group_id = 'forum' and permission_id = 'viewStaff'");
	}

    public function uninstallStep1(): void
    {
        $this->executeUpgradeQuery("delete from xf_permission_entry where permission_group_id = 'forum' and permission_id = 'viewStaff'");
        $this->executeUpgradeQuery("delete from xf_permission_entry where permission_group_id = 'forum' and permission_id = 'viewStickies'");
    }
}