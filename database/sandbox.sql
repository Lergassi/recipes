select
    rc.*
from recipe_commits rc
    left join heads h on rc.id = h.recipe_commit_id
# where rc.recipe_id = 1 and rc.id = h.recipe_commit_id
where h.recipe_id = 6
;