<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/opencaching.php';

class Logs_m extends CI_Model {


    function __construct()
        {
                // Call the Model constructor
                        parent::__construct();
                            }

function getClogs($tables,$sql_where,$limit,$offset)
{

/*
$rsLogs = "SELECT 	
				`cache_logs`.`id` AS `log_id`,
				`cache_logs`.`picturescount` AS `picturescount`,
				`cache_logs`.`user_id` AS `finder_id`,
				`user`.`username` AS `username`,
				`cache_logs`.`date` AS `date`,
				`cache_logs`.`type` AS `type`,
				`cache_logs`.`text` AS `text`,
				`cache_logs`.`text_html` AS `text_html`,
				`log_types_text`.`text_listing` AS `type_name`
				FROM `cache_logs` $tables
				INNER JOIN `log_types` ON `log_types`.`id`=`cache_logs`.`type`
				INNER JOIN `log_types_text` ON `log_types_text`.`log_types_id`=`log_types`.`id` AND `log_types_text`.`lang`='pl'
				INNER JOIN `user` ON `user`.`user_id` = `cache_logs`.`user_id`, caches
				WHERE `cache_logs`.`deleted` = 0 
				$sql_where 
				ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`Id` DESC LIMIT 5";


 */
 $rsLogs ="SELECT `cache_logs`.`id` `log_id`, 
		    `cache_logs`.`type`, 
		    `log_types`.`en` `type_name` ,
		    `cache_logs`.`date`, 
		    `user`.`username` `finder`, 
		    `cache_logs`.`user_id` `finder_id`, 
		    `cache_logs`.`text`
	    FROM `cache_logs`, `log_types`,`user` $tables
	    WHERE `log_types`.`id`=`cache_logs`.`type` AND `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id` $sql_where ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id`  DESC LIMIT $offset,$limit"; 

$query_logs = $this->db->query($rsLogs);

$all_logs="";

foreach ($query_logs->result() as $row)
{

$log='';

	$log['log']['id']=$row->log_id;
	$log['log']['date']=$row->date;
	$log['log']['finder']=$row->finder;
	$log['log']['finder_id']=$row->finder_id;
	$log['log']['type_id']=$row->type;
	$log['log']['type_name']=$row->type_name;
	$log['log']['text']=cleanup_text($row->text);
	
	$all_logs[]=$log;
	}
	
//print_r($alogs);
//$all_logs[]=$logs;


return $all_logs;

}


}