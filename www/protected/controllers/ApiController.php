<?php
/**
 * @author Капенкин Дмитрий <dkapenkin@rambler.ru>
 * @date 07.05.14
 * @time 16:38
 * Created by JetBrains PhpStorm.
 */
class ApiController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array();
    }

    // Actions
    public function actionCinema($name)
    {
        $this->_getShedule($name);
    }

    public function actionCinemahall($name,$hall){
        $this->_getShedule($name,$hall);
    }

    public function actionFilm($name){
        /** @var $sessions Session[] */
        $sessions = Session::model()->with(array(
            'film'=>array(
                'select'=>false,
                'condition'=>'film.name = :n',
                'params'=>array(':n'=>$name),
            )
        ))->findAll();
        if(!empty($sessions)){
            $data = array();
            foreach($sessions as $session){
                if(!isset($data[$session->hall->cinema_id])){
                    $data[$session->hall->cinema_id] = array("cinema_name"=>$session->hall->cinema->name);
                }
                if(!isset($data[$session->hall->cinema_id][$session->hall_id])){
                    $data[$session->hall->cinema_id][$session->hall_id] = array("hall_number"=>$session->hall->number,"hall_capacity"=>$session->hall->seats);
                }
                array_push($data[$session->hall->cinema_id][$session->hall_id],array(
                    "session_id" => $session->id,
                    "date" => $session->date,
                ));
            }
            if(empty($data)){
                $this->_sendResponse(200,
                    sprintf('No items where found for name <b>%s</b>', $name) );
            } else {
                $this->_sendResponse(200, CJSON::encode($data));
            }
        } else {
            $this->_sendResponse(200,
                sprintf('The sessions for film <b>%s</b> is not found', $name) );
        }
    }

    public function actionSession($id){
        /** @var $session Session */
        $session = Session::model()->findByPk($id);
        if(isset($session)){
            $data = array(
                "hall_capacity" => $session->hall->seats,
                "reserved_seats" => $this->_getReservedSeats($session),
            );
            $this->_sendResponse(200, CJSON::encode($data));
        } else {
            $this->_sendResponse(200,
                sprintf('The sessions <b>%s</b> is not found', $id) );
        }
    }

    public function actionTicketsbuy($id,$places){
        /** @var $model Session */
        $model = Session::model()->findByPk($id);
        if(!isset($model)){
            $this->_sendResponse(200,
                sprintf('The sessions <b>%s</b> is not found', $id) );
        }
        $_p = CJSON::decode($places);
        if(!isset($_p) || empty($_p)){
            $this->_sendResponse(200,'Incorrect data format of places or empty data. Use format: [1,2,3]');
        }
        $reservedSeats = $this->_getReservedSeats($model);
        foreach($_p as $place){
            if(false !== array_search($place,$reservedSeats)){
                $this->_sendResponse(200,
                    sprintf('The places <b>%s</b> is already reserved', $place) );
            }
            if(intval($place)>$model->hall->seats){
                $this->_sendResponse(200,
                    sprintf('Maximum number of places <b>%s</b>', $model->hall->seats) );
            }
            if(intval($place)<1){
                $this->_sendResponse(200,
                    sprintf('Minimum number of places <b>%s</b>', 1) );
            }
        }
        $ticket = new Tickets;
        $ticket->session_id = $id;
        $ticket->places = $places;
        if($ticket->save()){
            $this->_sendResponse(200,
                sprintf('Your ticket number <b>%s</b>', $ticket->id) );
        } else {
            $this->_sendResponse(500);
        }
    }

    public function actionTicketsreject($id){
        /** @var $model Tickets */
        $model = Tickets::model()->findByPk($id);
        if(!isset($model)){
            $this->_sendResponse(200,
                sprintf('The ticket <b>%s</b> is not found', $id) );
        }
        $user_time = time();
        $ticket_time = strtotime($model->session->date);
        $max_reject_time = 60*60; // 1 hour
        if($ticket_time-$user_time < $max_reject_time){
            $this->_sendResponse(200,'You can cancel your reservation at least one hour before the session');
        } else {
            if($model->delete()){
                $this->_sendResponse(200,
                    sprintf('Your ticket number <b>%s</b> is canceled', $id) );
            } else {
                $this->_sendResponse(500);
            }
        }
    }

    /**
     * @param $session Session
     * @return array
     */
    private function _getReservedSeats($session){
        $_ = array();
        foreach($session->tickets as $ticket){
            /** @var $ticket Tickets */
            $_ = array_merge(
                $_,
                CJSON::decode($ticket->places)
            );
        }
        return $_;
    }

    private function _getShedule($name,$hall=null){
        /** @var $model Cinema */
        $model = Cinema::model()->findByAttributes(array("name"=>$name));
        if(isset($model)){
            $data = array();
            if(isset($hall)){
                $CinemaHalls = $model->cinemaHalls(array('condition'=>"number = :n","params"=>array(":n"=>$hall)));
            } else {
                $CinemaHalls = $model->cinemaHalls;
            }
            foreach($CinemaHalls as $CinemaHall){
                $data[$CinemaHall->id] = array("hallNumber"=>$CinemaHall->number,"shedule"=>array());
                foreach($CinemaHall->sessions as $session){
                    array_push($data[$CinemaHall->id]["shedule"],array(
                        "film"=>$session->film->name,
                        "date"=>$session->date,
                        "description"=>$session->description,
                    ));
                }
            }
            if(empty($data)){
                $this->_sendResponse(200,
                    sprintf('No items where found for name <b>%s</b>', $name) );
            } else {
                $this->_sendResponse(200, CJSON::encode($data));
            }
        } else {
            $this->_sendResponse(200,
                sprintf('The cinema called <b>%s</b> is not found', $name) );
        }
    }

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);

        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templated in a real-world solution
            $body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
    <hr />
    <address>' . $signature . '</address>
</body>
</html>';

            echo $body;
        }
        Yii::app()->end();
    }

    private function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}
