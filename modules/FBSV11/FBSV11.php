<?php
class FBSV11 extends CodonModule
{
	public $title = 'Flight Booking System V2.0';
	
	public function index() {
            if(isset($this->post->action))
            {
                if($this->post->action == 'findflight') {
                $this->findflight();
                }
            }
            else
            {
            $this->set('airports', OperationsData::GetAllAirports());
            $this->set('airlines', OperationsData::getAllAirlines());
            $this->set('aircrafts', FBSV2Data::findaircrafttypes());
            $this->set('countries', FBSV2Data::findcountries());
            $this->show('fbsv/airport_search.php');
            }
        }

        public function findflight()
	{
		$arricao = DB::escape($this->post->arricao);
                $depicao = DB::escape($this->post->depicao);
                $airline = DB::escape($this->post->airline);
                $aircraft = DB::escape($this->post->aircraft);
                
                if(!$airline)
                    {
                        $airline = '%';
                    }
                if(!$arricao)
                    {
                        $arricao = '%';
                    }
                if(!$depicao)
                    {
                        $depicao = '%';
                    }
                if($aircraft == !'')
                {
                    $aircrafts = FBSV2Data::findaircraft($aircraft);
                    foreach($aircrafts as $aircraft)
                    {
                        $route = FBSV2Data::findschedules($arricao, $depicao, $airline, $aircraft->id);
                        if(!$route){$route=array();}
                        if(!$routes){$routes=array();}
                        $routes = array_merge($routes, $route);
                    }
                }
                else
                {
                $routes = FBSV2Data::findschedule($arricao, $depicao, $airline);
                }

		$this->set('allroutes', $routes);
		$this->show('fbsv/schedule_results.php');
                
	}
	
	public static function jumpseat()  {
        $icao = DB::escape($_GET['depicao']);
        $this->set('airport', OperationsData::getAirportInfo($icao));
        $this->set('cost', DB::escape($_GET['cost']));
        $this->show('fbsv/jumpseatconfirm.php');
    }

    public static function purchase()  {
       
               $id = DB::escape($_GET['id']);
               $cost = $_GET['cost'];
               $curmoney = Auth::$userinfo->totalpay;
               $total = ($curmoney - $cost);
               FBSV2Data::purchase_ticket(Auth::$userinfo->pilotid, $total);
               FBSV2Data::update_pilot_location($id);
                           
    }
}