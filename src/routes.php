<?php
// Routes

$app->get('/getusers', function ($request, $response) {
		$handler = new IOhandler;
        $sth = $handler->getAll('chatty');
        return $this->response->withJson($sth);
    });

$app->post('/login', function($request, $response){
	$data = json_decode($request->getBody());
	$handler = new IOhandler;
	$username = $data->username;
	$password = $data->password;
	$orderbyparam = "_id";
	$sth = $handler->login('users', 'imm', 'magnitudea', $userparam, $orderbyparam);
	return $this->response->withJson($sth);
});

$app->post('/register', function($request, $response){
	$data = json_decode($request->getBody());
	$handler = new IOhandler;
	$username = $data->username;
	$password = $data->password;
	$email = $data->email;
	$mobile_number = $data->mobile_number;
	$about_me = $data->about_me;
	$d_o_b = $data->date_of_birth;
	$date_created = date("Y-m-d h:i:sa");
	$sth = $handler->insert('users', array('username', 'password', 'email', 'mobile_num', 'about_me', 'date_of_birth','date_registered'), array($username, $password, $email, $mobile_number, $about_me, $d_o_b, $date_created));
	return $this->response->withJson($sth);
});

$app->post('/sendMsg', function($request,$response){
	$data = json_decode($request->getBody());
	$message = $data->message;
	$handler = new IOhandler;
	$id = $handler->getSessiondata();
	$senderid =  $id["userid"];
	$receiverid =  $data->receiverid;
	$date_created = date("Y-m-d h:i:sa");
	$sth = $handler->insert('messages', array('sender_id', 'receiver_id', 'time_sent', 'message'), array('1', $receiverid, $date_created, $message));
	return $this->response->withJson($sth);
});

$app->get('/getreceiveMsg', function($request, $response){
	$handler = new IOhandler;
	$id = $handler->getSessiondata();
	$userid =  $id["userid"];
    $sth = $handler->get_all('messages', 'receiver_id', '12');
    return $this->response->withJson($sth);
});

$app->get('/getsentMsg', function($request, $response){
	$handler = new IOhandler;
	$id = $handler->getSessiondata();
	$userid =  $id["userid"];
    $sth = $handler->get_all('messages', 'sender_id', '12');
    return $this->response->withJson($sth);
});

