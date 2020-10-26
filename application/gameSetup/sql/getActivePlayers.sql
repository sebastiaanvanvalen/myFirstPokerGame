SELECT act_table_1.player_id, players.player_name, player_age FROM act_table_1 
INNER JOIN players ON act_table_1.player_id = players.id
WHERE act_table_1.active = 1