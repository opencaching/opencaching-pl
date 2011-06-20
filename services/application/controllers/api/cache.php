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

class Cache extends REST_Controller
{
 
 
public function index_get()
    {
    
 // load model caches
 $this->load->model('caches/caches_m');

    // initial WHERE sql filter
    $sql_where=" AND caches.status!='4' AND caches.status!='5' AND caches.status!='6'";
    $tables="";
    $wp="";
    $owner="";
    $modifiedsience="";
    $api_key=$this->input->get('api-key');
    $sqluid=$this->db->query("SELECT `user_id` FROM `keys` WHERE `key`='$api_key'");
    
    $row=$sqluid->row();
    $userid=$row->user_id;

    // check requite param

    if ($this->input->get('status'))
    {
    $st=$this->input->get('status');
    $sta=explode(",",trim($st));
    $stat[]="";
    foreach ($sta as $ss){
	if ($ss==1 || $ss==2 || $ss==3)
	{ $stat[]=$ss;}
	}
    $s="";
    for ($i=0; $i<count($stat); $i=$i+1){
        if ($stat[$i]==1 || $stat[$i]==2 || $stat[$i]==3)
	    {
	    $s.=$stat[$i];
	    if ($i!=count($stat)-1) {$s.=",";}
	    }
	    }
	    $sql_where.=" AND caches.status IN(".$s.")";

    } else { $sql_where.=" AND caches.status='1'";}
    
    if ($this->input->get('format'))
    {
    $format=$this->input->get('format');
    } else {
    $format="gpx";} 
    
    
    if ($this->input->get('area'))
    {
    $area=$this->input->get('area');
    //$area=str_replace(':',',',$a);
    } else { $area="0,0,0,0";}

    if ($this->input->get('point'))
    {
    $point=$this->input->get('point');
    } else { $point="0,0";}


    if ($this->input->get('wp'))
    {
    $wp=$this->input->get('wp');
    $sql_where.=" AND caches.wp_oc='".$wp."'";
    }

    if ($this->input->get('owner'))
    {
    $owner=$this->input->get('owner');
    //$owner=str_replace(':',',',$o);
    $sql_where.=" AND caches.user_id IN (".$owner.")";
    }

    if ($this->input->get('found'))
    {
    $found=$this->input->get('found');
    if ($found=="true"){
    $sql_where.=" AND cache_logs.cache_id=caches.cache_id AND cache_logs.type='1' AND cache_logs.user_id=$userid";
    $tables .=",cache_logs";
    }
    if ($found=="false"){
    $sql_where.=" AND cache_logs.cache_id=caches.cache_id AND cache_logs.type='1' AND cache_logs.user_id!=$userid";
    $tables.=",cache_logs";
    }
    }

    if ($this->input->get('skipmy'))
    {
    $skipmy=$this->input->get('skipmy');
    if ($skipmy=="true"){
    $sql_where.=" AND caches.user_id!=$userid";
	}
    }

    //modifiedsience
    if ($this->input->get('modifiedsience'))
    {
    $dModifiedsince=$this->input->get('modifiedsience');

		if (mb_strlen($dModifiedsince) != 14)
		{
			echo 'Invalid modifiedsince value (wrong length)';
			exit;
		}
		
		// convert to time
		$nYear = mb_substr($dModifiedsince, 0, 4);
		$nMonth = mb_substr($dModifiedsince, 4, 2);
		$nDay = mb_substr($dModifiedsince, 6, 2);
		$nHour = mb_substr($dModifiedsince, 8, 2);
		$nMinute = mb_substr($dModifiedsince, 10, 2);
		$nSecond = mb_substr($dModifiedsince, 12, 2);
		
		if ((!is_numeric($nYear)) && (!is_numeric($nMonth)) && (!is_numeric($nDay)) && (!is_numeric($nHour)) && (!is_numeric($nMinute)) && (!is_numeric($nSecond)))
		{
			echo 'Invalid modifiedsince value (non-numeric content)';
			exit;
		}
		
		if (($nYear < 1970) || ($nYear > 2100) 
				|| ($nMonth < 1) || ($nMonth > 12)
				|| ($nDay < 1) || ($nDay > 31)
				|| ($nHour < 0) || ($nHour > 23)
				|| ($nMinute < 0) || ($nMinute > 59)
				|| ($nSecond < 0) || ($nSecond > 59))
		{
			echo 'Invalid modifiedsince value (value out of range)';
			exit;
		}
		$sModifiedSince = date('Y-m-d H:i:s', mktime($nHour, $nMinute, $nSecond, $nMonth, $nDay, $nYear));

		$sql_where.= " AND `caches`.`last_modified` > '".$sModifiedSince."' ";
} 


    // check requited prameters

    if($area!="0,0,0,0" || $point!="0,0" || $wp!="" || $owner!="" || $sqlmodifiedsience=!"")
    {


    if ($this->input->get('dist'))
    {
    $dist=$this->input->get('dist');
    } else { $dist="0";}
    

    if ($this->input->get('desc')|| $format=="gpx")
    {
    $d=$this->input->get('desc');
    if($d="true")
    $desc=",`cache_desc`.`hint` `hint`,`cache_desc`.`short_desc` `short_desc`,`cache_desc`.`desc` `desc`";
    } else { $desc="";}


    if ($this->input->get('recommend'))
    {
    $r=$this->input->get('recommend');
    $sql_where.=' AND caches.topratings >= \'' . $r .'\'';
    }

    if ($this->input->get('terrain'))
    {
    $t=$this->input->get('terrain');
    $terrain=explode("-",$t);
    $sql_where.= ' AND caches.terrain BETWEEN \'' . $terrain[0]*2 . '\' AND \'' . $terrain[1]*2 . '\'';
    }

    if ($this->input->get('difficulty'))
    {
    $d=$this->input->get('difficulty');
    $difficulty=explode("-",$d);
    $sql_where.= ' AND caches.difficulty BETWEEN \'' . $difficulty[0]*2 . '\' AND \'' . $difficulty[1]*2 . '\'';
    }
    if ($this->input->get('size'))
    {
    $si=$this->input->get('size');
    $size=explode("-",$si);
    $sql_where.= ' AND caches.size BETWEEN \'' . $size[0] . '\' AND \'' . $size[1] . '\'';
    }

    if ($this->input->get('score'))
    {
    $s=$this->input->get('score');
    $score=explode("-",$s);
    $sql_where.= ' AND caches.score BETWEEN \'' . $score[0] . '\' AND \'' . $score[1] . '\' AND caches.votes > 3';
    }

    if ($this->input->get('type'))
    {
    $type=$this->input->get('type');
    //$type=str_replace(':',',',$t);
    $sql_where.=" AND caches.type IN (".$type.")";
    }
    if ($this->input->get('attrib'))
    {
    $attrib=$this->input->get('attrib');
    //$attrib=str_replace(':',',',$t);
    $sql_where.=" AND caches.cache_id=caches_attributes.cache_id AND caches_attributes.attrib_id IN (".$attrib.")";
    $tables.=",caches_attributes";
    }

    if ($this->input->get('limit'))
    {
    $n_caches=$this->input->get('limit');
    // set max limit 1000
    if ($n_caches>1000) $n_caches="1000";
    $limit=" LIMIT ".$n_caches;
    } else { $limit=" LIMIT 100";}

    if ($this->input->get('logs'))
    {
    $n_logs=$this->input->get('logs');
    // set max limit 500
    if ($n_logs>500) $n_logs="500";
    $log_limit=$n_logs;
    } else { $log_limit="0";}


	// get result
	$caches=$this->caches_m->getCaches($area,$point,$dist,$desc,$sql_where,$tables,$limit,$log_limit,$api_key);
    
        
        if($caches)
        {
            $this->response($caches, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any geocaches'), 404);
        }
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any geocaches.'), 404);
        }
    
    
    
    }



}