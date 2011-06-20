<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		sp2ong
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Log extends REST_Controller
{
 
 
public function index_get()
    {
    
 // load model logss
 $this->load->model('logs/logs_m');

// initial WHERE sql filter
$sql_where="";
$tables="";
$wp='';
$userid="";
$logid="";
$format="xml";

if ($this->input->get('format'))
{
$format=$this->input->get('format');
if ($format="gpx") $format="xml";
}

if ($this->input->get('userid'))
{
$userid=$this->input->get('userid');
//$user=str_replace(':',',',$u);
$sql_where.=" AND cache_logs.user_id IN (".$userid.")";
}

if ($this->input->get('wp'))
{
$wp=$this->input->get('wp');
$tables=",caches ";
//$user=str_replace(':',',',$u);
$sql_where.=" AND `caches`.`wp_oc`='".$wp."'";
}

if ($this->input->get('id'))
{
$logid=$this->input->get('id');
$sql_where = " AND `cache_logs`.`id` =".$logid."";
$limit=1;
}

        if($logid!="" || $wp!="" || $userid!="" )
        {

if ($this->input->get('limit'))
{
$n_logs=$this->input->get('limit');
// set max limit 1000
if ($n_logs>1000) $n_logs="1000";
$limit=$n_logs;
} else { $limit="5";}

if ($this->input->get('offset'))
{
$offset=$this->input->get('offset');
} else { $offset="0";}


if ($this->input->get('type'))
{
$type=$this->input->get('type');
//$type=str_replace(':',',',$t);
$sql_where.=" AND cache_logs.type=".$type."";
}



	$cache_logs=$this->logs_m->getClogs($tables,$sql_where,$limit,$offset);
    
        
        if($cache_logs)
        {
            $this->response($cache_logs, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any logs!'), 404);
        }

        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any logs!'), 404);
        }


    }


}