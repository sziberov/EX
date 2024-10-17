SELECT o.*
FROM objects AS o
LEFT JOIN settings AS s_0 ON s_0.object_id = o.id AND s_0.`key` = 'awaiting_save'
LEFT JOIN settings AS s_1 ON s_1.object_id = o.id AND s_1.`key` = 'hide_from_search'
LEFT JOIN settings AS s_2 ON s_2.object_id = o.user_id AND s_2.`key` = 'hide_from_search'
JOIN links AS l ON l.from_id = 1 AND l.to_id = o.id AND l.type_id = 1
JOIN settings AS s_3 ON s_3.link_id = l.id AND s_3.`key` = 'access_level_id' AND s_3.value BETWEEN 1 AND 5
WHERE o.type_id != 4  -- Объект не должен быть общим
  AND (s_0.value != 'true' OR s_0.value IS NULL)  -- Объект должен быть явно или неявно сохранён
  AND (s_1.value != 'true' OR s_1.value IS NULL)  -- Объект не должен быть скрыт от поиска
  AND (s_2.value != 'true' OR s_2.value IS NULL)  -- Автор не должен быть скрыт от поиска