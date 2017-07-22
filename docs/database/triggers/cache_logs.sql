--
-- 
--
CREATE TRIGGER `cl_update` AFTER UPDATE ON `cache_logs`
    FOR EACH ROW begin
        IF ( old.type = 1 ) THEN
            IF ( new.deleted = 1 and old.deleted = 0  ) OR ( new.type <> 1 ) OR
                ( date(new.date) <> date( old.date )      
            ) THEN
             
                IF EXISTS (
	                  SELECT 1 FROM user_finds 
	                  WHERE date = date(old.date) AND user_id = new.user_id
                ) THEN 
	                  
					          UPDATE user_finds SET number = number - 1 
					          WHERE date = date(old.date) and user_id = new.user_id;
                
                END if;                           
            END if;
        END if;

				IF ( new.deleted = 0 and new.type = 1) THEN
				
					  IF ( old.deleted = 1 ) OR ( old.type <> 1 ) OR
					     ( date(new.date) <> date( old.date )  
					  ) THEN
					    
						    IF EXISTS (
						        SELECT 1 FROM [ 
						        WHERE date = date(new.date) and user_id = new.user_id
						    ) THEN 
						  
						      UPDATE user_finds SET number = number + 1 
						      WHERE date = date(new.date) and user_id = new.user_id;
						      
						    ELSE
						            
						      INSERT into user_finds (date, user_id, number ) 
						      VALUES ( new.date, new.user_id, 1 );
						      
						    END IF;
					  END IF;      
				END if;      
  END -- FOR EACH ROW



CREATE TRIGGER `cl_insert` AFTER INSERT ON `cache_logs`
    FOR EACH ROW BEGIN 
	    
        IF( new.deleted=0 AND new.type=1 ) THEN
            IF EXISTS (
                SELECT 1 FROM user_finds
                WHERE date = date( new.date ) AND user_id=new.user_id
            ) THEN
        
                UPDATE user_finds SET number=number+1 
                WHERE date = date( new.date ) AND user_id=new.user_id;

            ELSE 
            
                INSERT INTO user_finds( date, user_id, number )
                VALUES ( new.date, new.user_id, 1 );

            END IF ;
        END IF ;
    END -- FOR EACH ROW

    