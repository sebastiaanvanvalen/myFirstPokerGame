INSERT INTO chats (player_id, player_name, msg, create_time, created_by) 
VALUES (:player_id, :player_name, :msg, SYSDATE(), "501")