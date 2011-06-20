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

class User extends REST_Controller
{
 
 
public function index_get()
    {
    
 // load model logss
 $this->load->model('users/users_m');

// initial WHERE sql filter
$sql_where="";
$tables="";
$username='';
$userid="";
$format="xml";
//$this->format->"xml";

if ($this->input->get('format'))
{
$format=$this->input->get('format');
if ($format="gpx") $format="xml";
}

if ($this->input->get('userid'))
{
$userid=$this->input->get('userid');
//$user=str_replace(':',',',$u);
$sql_where.=" AND cache_logs.user_id=".$userid."";
}

/*
if ($this->input->get('username'))
{
$wp=$this->input->get('username');
$tables=",caches ";
//$user=str_replace(':',',',$u);
$sql_where.=" AND `caches`.`wp_oc`='".$wp."'";
}
*/



	$users=$this->users_m->getClogs($tables,$sql_where);
    
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any info about user!'), 404);
        }

        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any info about user!'), 404);
        }


    }


}