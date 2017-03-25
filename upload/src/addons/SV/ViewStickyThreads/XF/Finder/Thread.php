<?php

namespace SV\ViewStickyThreads\XF\Finder;

class Thread extends XFCP_Thread
{
    public function applyVisibilityChecksInForum(\XF\Entity\Forum $forum)
    {
        $return = parent::applyVisibilityChecksInForum($forum);
        $visitor = \XF::visitor();

        if ($visitor->hasNodePermission($forum->node_id, 'viewOthers') || !$visitor->hasNodePermission($forum->node_id, 'viewStickies'))
        {
            return $return;
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
// view my moderated + deleted
array:3 [?
  0 => "`xf_thread`.`node_id` = 2"
  1 => "(`xf_thread`.`discussion_state` = 'moderated' AND `xf_thread`.`user_id` = 1) OR (`xf_thread`.`discussion_state` IN ('visible', 'deleted'))"
  2 => "`xf_thread`.`user_id` = 1"
]
*/
        // We edit the "`xf_thread`.`user_id` = 1" statement, as this is what enforces the lack of viewOthers permission
        foreach($return->conditions as $key => $condition)
        {
            if (strpos($condition, '`xf_thread`.`user_id`') !== false &&
                strpos($condition, '`xf_thread`.`discussion_state` = \'moderated\' AND `xf_thread`.`user_id` = ') === false)
            {
                $parts = [];
                $parts[] = '`xf_thread`.`sticky` = 1';

                if ($parts)
                {
                    $return->conditions[$key] = "({$condition} OR (`xf_thread`.`discussion_state` = 'visible' AND (". implode(' OR ', $parts) .')))';
                }
                break;
            }
        }

        return $return;
    }
}