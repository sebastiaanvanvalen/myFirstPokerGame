SELECT id FROM players 
WHERE temp_id = :temp_id
AND create_time > SYSDATE() - INTERVAL 10 MINUTE