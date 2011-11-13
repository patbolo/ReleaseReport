<?php

class JIRAGateway implements IProjectManagementSystem{

	static $authSegment = '/auth/1/session';

	static $apiSegment = '/api/2.0.alpha1/issue/';

	static $expiry = 0;
	
	protected $cookie;
	
	protected $client;
	
	private $baseURL;
	
	private $username;
	
	private $password;
	
	private $regex;

	public function __construct($baseURL, $username, $password, $regex){
		$this->baseURL = $baseURL;
		$this->username = $username;
		$this->password = $password;
		$this->regex = $regex;
	}
	
	private function getClient(){
		if (!isset($this->client)){
			$this->client = new RestfulService($this->baseURL.'/rest', self::$expiry);
			$this->client->httpHeader('Content-Type: application/json');
		}
		return $this->client;
	}

	protected function getAuthenticatedClient(){
		if (isset($this->cookie)) return $this->client;
		$response = $this->getClient()->request(self::$authSegment, 'POST', '{"username":"'.$this->username.'","password":"'.$this->password.'"}');
		$session = (json_decode($response->getBody()));
		if (!isset($session->session->value)) user_error('Error when authenticating against the JIRA API');
		$this->cookie = $session->session->value;
		$this->client->httpHeader('Cookie: JSESSIONID=' . $this->cookie);
		return $this->client;
	}

	protected function getTicket($ticketID){
		$response = $this->getAuthenticatedClient()->request(self::$apiSegment.$ticketID);
		$body = (json_decode($response->getBody()));
		if (isset($body->key) && ($body->key == $ticketID)){
			$ticket = new Ticket();
			$ticket->Key = $body->key;
			$ticket->Summary = @$body->fields->summary->value;
			$ticket->Priority = @$body->fields->priority->value->name;
			$ticket->Status = @$body->fields->status->value->name;
			$ticket->Assignee = @$body->fields->assignee->value->displayName;
			if (isset($body->fields->comment->value)){
				$latestComment = array_pop($body->fields->comment->value);
				if (is_object($latestComment)) {
					$ticket->LastComment = $latestComment->body;
					if (isset($latestComment->author->displayName)) $ticket->LastComment.= '('. $latestComment->author->displayName.')';
				}
			}
			return $ticket;
		}
	}

	public function getTickets($commits){
		$tickets = new ArrayList();
		foreach ($commits as $releaseID=>$commitMsg){
			preg_match_all('/'.$this->regex.'/', $commitMsg, $matches);
			//TODO Cater for complex regex, with multiple tickets in the commit message
			if ($matches) {
				foreach($matches as $match){
					foreach($match as $ticketID){
						$tickets->push($this->getTicket($ticketID));
					}
				}
			}
		}
		return $tickets;
	}
}