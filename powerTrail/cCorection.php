<?php

class cCorection
{
	private $userId = 0;
	private $latitude = 0;
	private $longitude = 0;
	private $userArray = array();

	public function __construct($params)
	{
		$this->latitude = $params['latitude'];
		$this->longitude = $params['longitude'];
		$this->userId = $params['userId'];
		$userrCollection = new UserCollection();
		$this->userArray = $userrCollection->getUserCollection();
		$this->process();
	}

	public function getLatitude()
	{
		return $this->latitude;
	}

	public function getLongitude()
	{
		return $this->longitude;
	}

	private function process()
	{
		if(in_array($this->userId, $this->userArray)){
			$this->updateCoords();
		}
	}

	private function updateCoords(){
		$meters = rand(30,60); //Number of meters to calculate coords for north/south/east/west
		$equatorCircumference = 6371000; //meters
		$polarCircumference = 6356800; //meters
		$mPerDegLong = 360 / $polarCircumference;
		$radLat = ($this->latitude * M_PI / 180); //convert to radians, cosine takes a radian argument and not a degree argument
		$mPerDegLat = 360 / (cos($radLat) * $equatorCircumference);
		$degDiffLong = $meters * $mPerDegLong;  //Number of degrees latitude as you move north/south along the line of longitude
		$degDiffLat = $meters * $mPerDegLat; //Number of degrees longitude as you move east/west along the line of latitude
		$this->calcNewCoords($degDiffLong, $degDiffLat);
	}

	private function calcNewCoords($degDiffLong, $degDiffLat) {
		$direction = rand(1,8);
		switch ($direction) {
			case 1:
				$this->latitude = $this->latitude + $degDiffLat;
				break;
			case 2:
				$this->longitude = $this->longitude + $degDiffLong;
				break;
			case 3:
				$this->latitude = $this->latitude - $degDiffLat;
				break;
			case 4:
				$this->longitude = $this->longitude - $degDiffLong;
				break;
			case 5:
				$this->latitude = $this->latitude + $degDiffLat;
				$this->longitude = $this->longitude + $degDiffLong;
				break;
			case 6:
				$this->latitude = $this->latitude + $degDiffLat;
				$this->longitude = $this->longitude - $degDiffLong;
				break;
			case 7:
				$this->latitude = $this->latitude - $degDiffLat;
				$this->longitude = $this->longitude - $degDiffLong;
				break;
			case 8:
				$this->latitude = $this->latitude - $degDiffLat;
				$this->longitude = $this->longitude + $degDiffLong;
		}
	}

}

final class UserCollection
{

	private $userArray = array();

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new UserCollection();
        }
        return $inst;
    }


    private function __construct()
    {
		include __DIR__.'/../lib/settings.inc.php';
		if(isset($userCollection)) {
			$this->userArray = $userCollection;
		}
    }

	public function getUserCollection()
	{
		return $this->userArray;
	}
}