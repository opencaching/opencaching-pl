<?php
/***************************************************************************
                                                                ./changelograting.php
                                                            -------------------
        begin                : March 20 2014
        copyright            : (C) 2004 The OpenCaching Group
        @author triPPer - triPPer1971@wp.pl

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

     Unicode Reminder メモ

     remove a watch from the watchlist

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

 //Preprocessing
    if ($error == false)
    {
        //cacheid
        $log_id = 0;
        if (isset($_REQUEST['logid']))
        {
            $log_id = intval($_REQUEST['logid']);
        }

        //user logged in?
        if ($usr == false)
        {            
            tpl_redirect('login.php');
        }
        else if ( !isset( $_REQUEST[ "logid"]) or !isset( $_REQUEST[ "target"]) or !isset( $_REQUEST[ "cacheid" ]) or !isset( $_REQUEST[ "posY" ]) )
        {
            tpl_redirect( "index.php" );
        }
        else
        {
            $nLogId= $_REQUEST[ "logid" ];
            $sTarget= $_REQUEST[ "target" ];
            $sCacheId= $_REQUEST[ "cacheid" ];
            $nPosY= $_REQUEST[ "posY" ];
            
            $query = "SELECT 1 FROM log_rating WHERE log_id =:1 and user_id=:2";
            
            $dbc = new dataBase();
            $dbc->multiVariableQuery($query, $nLogId, $usr[ "userid" ] );
        	
            if ( $dbc->rowCount() == 0 ) //add
            {        		
                $cDT = new DateTime();
                $currDate = $cDT->format('Y-m-d H:m:s');    

                $query = "INSERT INTO log_rating (log_id, user_id, date) VALUES( :1, :2, :3 )"; 
                $dbc->multiVariableQuery($query, $nLogId, $usr[ "userid" ], $currDate );
            }	
            else 
            {
                $query = "DELETE FROM log_rating WHERE log_id =:1 and user_id=:2";
                $dbc->multiVariableQuery($query, $nLogId, $usr[ "userid" ] );        		
            }                	
        }
        
        $sTarget .= "?cacheid=".$sCacheId."&posY=".$nPosY;
        
        tpl_redirect( $sTarget );
    }
?>
