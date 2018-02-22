<?php

namespace SV\ViewStickyThreads\XF\Entity;

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

        if (!$visitor->hasNodePermission($nodeId, 'viewStickies') || !$this->sticky)
        {
            return false;
        }

        // replicated from parent::canView
        if (!$this->Forum || !$this->Forum->canView())
        {
            return false;
        }

        if (!$visitor->hasNodePermission($nodeId, 'view'))
        {
            return false;
        }

        if (!$visitor->hasNodePermission($nodeId, 'viewContent'))
        {
            return false;
        }

        if ($this->discussion_state !== 'visible')
        {
            return false;
        }

        $error = null;
        return true;
    }
}
