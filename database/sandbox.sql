select
    rc.*
from recipe_commits rc
    left join heads h on rc.id = h.recipe_commit_id
# where rc.recipe_id = 1 and rc.id = h.recipe_commit_id
where h.recipe_id = 6
;

select
    rp.id
     ,rp.reference_product_id
#      ,rp.weight
#      ,r.name as r_name
     ,rfp.name
#      ,rcp.*
#      ,r.name as r_name
     ,h.recipe_commit_id as h_recipe_commit_id
     ,rc.id as rc_id
     ,rp.weight as rp_weight
     ,rcp.weight as rcp_weight
from recipe_positions rp
    left join recipes r on r.id = rp.recipe_id
    left join reference_products rfp on rfp.id = rp.reference_product_id
    left join heads h on h.recipe_id = r.id
    left join recipe_commits rc on rc.recipe_id = h.recipe_id
#     # выше пока всё работает...
    left join recipe_commit_positions rcp on rcp.recipe_commit_id = rc.id
#     inner join recipe_commit_positions rcp on rcp.recipe_commit_id = rc.id
#     left join recipe_commit_positions rcp on rcp.reference_product_id = rfp.id and rcp.recipe_commit_id = rc.id # вроде работает
#     inner join recipe_commit_positions rcp on rcp.recipe_commit_id = rc.id
#     right join recipe_commit_positions rcp on rcp.reference_product_id = rp.reference_product_id
where
    rp.recipe_id = 9
#     and rcp
#     and (rcp.weight is null)

#     and rcp.weight is null
#     and rcp.weight <> 300
#     and (rcp.weight <> rp.weight or rcp.weight is null)   # !!!
#     and (rcp.weight <> rp.weight)
#     and rcp.weight = 300
#     and rcp.weight is null
#     and rp.weight <> rcp.weight
#     and rp.weight = rcp.weight
#     and rp.weight >= rcp.weight
#     and rcp.recipe_commit_id = rc.id
#     and rp.reference_product_id = rcp.reference_product_id
#     and rp.weight <> rcp.weight
# where h.recipe_id = 9
;

select * from heads where recipe_id = 9;

select
    rp.*, rcp.id
from recipe_positions rp
#     inner join recipe_commit_positions rcp on rp.reference_product_id = rcp.reference_product_id
#     left join recipe_commit_positions rcp on rp.reference_product_id = rcp.reference_product_id
#     left join recipe_commit_positions rcp on rcp.reference_product_id = rp.reference_product_id
#     right join recipe_commit_positions rcp on rp.reference_product_id = rcp.reference_product_id
    inner join recipe_commit_positions rcp on rp.reference_product_id = rcp.reference_product_id
where
    rp.recipe_id = 9
#     and rp.reference_product_id = rcp.reference_product_id
;

select
    rp.*
from recipe_positions rp
#     inner join recipe_commit_positions rcp on rp.reference_product_id = rcp.reference_product_id
where rp.recipe_id = 9
;

select
    rcp.*
from recipe_commit_positions rcp
where
    rcp.recipe_commit_id = 50
;

select * from recipe_commit_positions;

select
#     rfp.*
    rp.*,
    rfp.name
from reference_products rfp
    left join recipe_positions rp on rp.reference_product_id = rfp.id
where
    rp.recipe_id = 9
;

select
#     rfp.*
    rp.*,
    rfp.name
from recipe_positions rp
    left join reference_products rfp on rfp.id = rp.reference_product_id
    left join recipes r on r.id = rp.recipe_id
    left join heads h on h.recipe_id = r.id
    left join recipe_commits rc on rc.recipe_id = h.recipe_id
    # ...
    left join recipe_commit_positions rcp on rcp.reference_product_id = rfp.id = rcp.reference_product_id
where
    rp.recipe_id = 9
#     and r.id = 9
#     and h.recipe_id = 9
#     and rc.recipe_id = 9
#     and rcp.recipe_commit_id = rc.id
#     and rcp.recipe_commit_id = 50
;

select
    rfp.*
from reference_products rfp
    left join recipe_positions rp on rfp.id = rp.reference_product_id
    left join recipe_commit_positions rcp on rcp.reference_product_id = rfp.id = rcp.reference_product_id
where
    rp.recipe_id = 9

select * from recipe_commit_positions where weight <> 300
;

# ---------------------------------------
select
    rcp.*
     ,rc.id
from recipe_commit_positions rcp
    left join recipe_commits rc on rc.id = rcp.recipe_commit_id
where
    rcp.recipe_commit_id = 50
;

# ...
select
    rcp.*
     ,rc.id
from recipe_commits rc
    left join recipe_commit_positions rcp on rcp.recipe_commit_id = rc.id
where
    rcp.recipe_commit_id = 50
;
# ---------------------------------------

select
    rcp.*
     ,rc.id
from recipe_commit_positions rcp
    right join recipe_commits rc on rc.id = rcp.recipe_commit_id
where
    rcp.recipe_commit_id = 50
;

select
    rcp.*
     ,rc.id
from recipe_commits rc
    inner join recipe_commit_positions rcp on rcp.recipe_commit_id = rc.id
where
    rcp.recipe_commit_id = 50
;

# Поиск разницы в рецептах. Новые позиции, измененные позиции, удаленные позиции.
# 0.0.2
select
    rp.reference_product_id
    ,rfp.name
    ,rcp.weight     # было, если null то продукт новый
    ,rp.weight      # стало

# debug
#     ,h.*
#     ,r.*
#     ,rp.*
#     ,rc.*
from recipe_positions rp
    left join reference_products rfp on rp.reference_product_id = rfp.id
    left join recipes r on rp.recipe_id = r.id
    left join heads h on r.id = h.recipe_id
    left join recipe_commits rc on h.recipe_commit_id = rc.id
    left join recipe_commit_positions rcp on rc.id = rcp.recipe_commit_id and rp.reference_product_id = rcp.reference_product_id
where
    r.id = 31
    and
    (
        rp.weight <> rcp.weight
        or rcp.weight is null
    )
union
select
    rcp.reference_product_id
    ,rfp.name
    ,rcp.weight     # было
    ,rp.weight      # стало, если null то продукт удален

from recipe_commit_positions rcp
    left join reference_products rfp on rcp.reference_product_id = rfp.id
    left join heads h on rcp.recipe_commit_id = h.recipe_commit_id
    left join recipe_commits rc on rcp.recipe_commit_id = rc.id and rc.id = h.recipe_commit_id # !!!
    left join recipes r on rc.recipe_id = r.id
    left join recipe_positions rp on r.id = rp.recipe_id and rcp.reference_product_id = rp.reference_product_id
where
    r.id = 31
    and rp.weight is null
;

# count
select count(*) as count
from (
    select
        rp.reference_product_id
        ,rfp.name
        ,rcp.weight     # было  если null удалено
        ,rp.weight as rp_weight      # стало
    from recipe_positions rp
        left join reference_products rfp on rp.reference_product_id = rfp.id
        left join recipes r on rp.recipe_id = r.id
        left join heads h on r.id = h.recipe_id
        left join recipe_commits rc on h.recipe_commit_id = rc.id
        left join recipe_commit_positions rcp on rc.id = rcp.recipe_commit_id and rp.reference_product_id = rcp.reference_product_id
    where
        r.id = 9
        and rp.weight <> rcp.weight
        or rcp.weight is null
    union
    select
        rcp.reference_product_id
        ,rfp.name
        ,rcp.weight     # было
        ,rp.weight      # стало если null удалено
    from recipe_commit_positions rcp
        left join reference_products rfp on rcp.reference_product_id = rfp.id
        left join heads h on rcp.recipe_commit_id = h.recipe_commit_id
        left join recipe_commits rc on rcp.recipe_commit_id = rc.id and rc.id = h.recipe_commit_id # !!!
        left join recipes r on rc.recipe_id = r.id
        left join recipe_positions rp on r.id = rp.recipe_id and rcp.reference_product_id = rp.reference_product_id
    where
        r.id = 9
        and rp.weight is null
     ) as sum_table
;