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

        // ensure the forum/node can actually be seen
        if ($visitor->hasNodePermission($nodeId, 'view') || $this->discussion_state != 'visible')
        {
            return false;
        }

        if ($visitor->hasNodePermission($nodeId, 'viewStickies') && $this->sticky)
        {
            return true;
        }

        return false;
    }
}