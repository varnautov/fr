SELECT
    MIN(id) AS min_id, MIN(group_id) AS group_id, COUNT(*) `count`
FROM (
SELECT
    @r := @r + (@group_id != group_id OR @group_id IS NULL) AS gn,
    @group_id := group_id AS sn,
    s.id,
    s.group_id
FROM (
SELECT
    @r := 0,
    @group_id := NULL
) vars,
test s
) q
GROUP BY gn