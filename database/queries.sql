# todo: Придумать названия запросам?
# Запрос на получение различий с последнего коммита и рецепта (diff).
# v.0.0.1
# select
#     rp.id
#     ,rp.reference_product_id
#     ,rfp.name
#     ,h.recipe_commit_id as h_recipe_commit_id
#     ,rc.id as rc_id
#     ,rp.weight as rp_weight
#     ,rcp.weight as rcp_weight
# from recipe_positions rp
#     left join recipes r on r.id = rp.recipe_id
#     left join reference_products rfp on rfp.id = rp.reference_product_id
#     left join heads h on h.recipe_id = r.id
#     left join recipe_commits rc on rc.recipe_id = h.recipe_id
#     left join recipe_commit_positions rcp on rcp.reference_product_id = rfp.id and rcp.recipe_commit_id = rc.id # вроде работает
# where
#     rp.recipe_id = 9
#     and (rcp.weight <> rp.weight or rcp.weight is null)
# ;

# Поиск разницы в рецептах. Новые позиции, измененные позиции, удаленные позиции.
# 0.0.1
select
    rp.reference_product_id
    ,rfp.name
    ,rcp.weight     # было  если null = удалено
    ,rp.weight      # стало
from recipe_positions rp
    left join reference_products rfp on rp.reference_product_id = rfp.id
    left join recipes r on rp.recipe_id = r.id
    left join heads h on r.id = h.recipe_id
    left join recipe_commits rc on h.recipe_commit_id = rc.id
    left join recipe_commit_positions rcp on rc.id = rcp.recipe_commit_id and rp.reference_product_id = rcp.reference_product_id
where
    r.id = :recipe_id
    and rp.weight <> rcp.weight
    or rcp.weight is null
union
select
    rcp.reference_product_id
    ,rfp.name
    ,rcp.weight     # было
    ,rp.weight      # стало если null = удалено
from recipe_commit_positions rcp
    left join reference_products rfp on rcp.reference_product_id = rfp.id
    left join heads h on rcp.recipe_commit_id = h.recipe_commit_id
    left join recipe_commits rc on rcp.recipe_commit_id = rc.id and rc.id = h.recipe_commit_id # !!!
    left join recipes r on rc.recipe_id = r.id
    left join recipe_positions rp on r.id = rp.recipe_id and rcp.reference_product_id = rp.reference_product_id
where
    r.id = :recipe_id
    and rp.weight is null
;

# Запрос на кол-во изменений.
# 0.0.1
# Версия подзапроса: 0.0.1
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
        r.id = :recipe_id
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
        r.id = :recipe_id
        and rp.weight is null
     ) as sum_table
;