<?php

namespace SV\ViewStickyThreads\XF\Finder;

class Thread extends XFCP_Thread
{
    public function applyVisibilityChecksInForum(\XF\Entity\Forum $forum, $allowOwnPending = false)
    {
        $finder = parent::applyVisibilityChecksInForum($forum, $allowOwnPending);
        $visitor = \XF::visitor();

        if ($visitor->hasNodePermission($forum->node_id, 'viewOthers') || !$visitor->hasNodePermission($forum->node_id, 'viewStickies'))
        {
            return $finder;
        }
/*
// view delete + moderated
array:3 [?
  0 => "`xf_thread`.`node_id` = 2"
  1 => "(`xf_thread`.`discussion_state` IN ('visible', 'deleted', 'moderated'))"
  2 => "`xf_thread`.`user_id` = 1"
]
// view all moderated
array:3 [?
  0 => "`xf_thread`.`node_id` = 2"
  1 => "(`xf_thread`.`discussion_state` IN ('visible', 'moderated'))"
  2 => "`xf_thread`.`user_id` = 1"
]
// view my moderated
array:3 [?
  0 => "`xf_thread`.`node_id` = 2"
  1 => "(`xf_thread`.`discussion_state` = 'moderated' AND `xf_thread`.`user_id` = 1) OR (`xf_thread`.`discussion_state` IN ('visible'))"
  2 => "`xf_thread`.`user_id` = 1"
]
// view visible (guest)
array:3 [?
  0 => "`xf_thread`.`node_id` = 2"
  1 => "(`xf_thread`.`discussion_state` IN ('visible'))"
  2 => "1=0"
]
// view my moderated + deleted
array:3 [?
  0 => "`xf_thread`.`node_id` = 2"
  1 => "(`xf_thread`.`discussion_state` = 'moderated' AND `xf_thread`.`user_id` = 1) OR (`xf_thread`.`discussion_state` IN ('visible', 'deleted'))"
  2 => "`xf_thread`.`user_id` = 1"
]
*/
        // map the columns to SQL encoded names.
        $stickyCol = $finder->columnSqlName('sticky');
        $userIdCol = $finder->columnSqlName('user_id');
        $discussionStateCol = $finder->columnSqlName('discussion_state');
        $moderatedCondition = "{$discussionStateCol} = 'moderated' AND {$userIdCol} = ";
        $isGuest = !$visitor->user_id;

        // We edit the "`xf_thread`.`user_id` = 1" statement, or for guests rewrite 1=0 as this is what enforces the lack of viewOthers permission
        foreach($finder->conditions as $key => $condition)
        {
            if ($isGuest && $condition === "1=0")
            {
                $finder->conditions[$key] = "$stickyCol = 1";
            }
            else if (strpos($condition, $userIdCol) !== false &&
                    strpos($condition, $moderatedCondition) === false)
            {
                $parts = [];
                $parts[] = "$stickyCol = 1";

                if ($parts)
                {
                    $finder->conditions[$key] = "($condition OR ($discussionStateCol = 'visible' AND (" . implode(' OR ', $parts) . ')))';
                }
                break;
            }
        }

        return $finder;
    }
}
