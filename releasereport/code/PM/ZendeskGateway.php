<?php

class ZendeskGateway implements IProjectManagementSystem{

	static $expiry = 0;

	protected $client;

	private $baseURL;

	private $username;

	private $password;

	private $regex;

	private $statusMap = array(
		'New',
		'Open',
		'Pending',
		'Solved',
		'Closed'
	);

	private $priorityMap = array(
		'No priority set',
		'Low',
		'Normal',
		'High',
		'Urgent'
	);

	public function __construct($baseURL, $username, $password, $regex){
		$this->baseURL = $baseURL;
		$this->username = $username;
		$this->password = $password;
		$this->regex = $regex;
	}

	private function getClient(){
		if (!isset($this->client)){
			$this->client = new RestfulService($this->baseURL, self::$expiry);
			$this->client->httpHeader('Content-Type: application/json');
			$this->client->basicAuth($this->username, $this->password);
		}
		return $this->client;
	}

	protected function getTicket($ticketID){
		$response = $this->getClient()->request('/tickets/'.$ticketID.'.json');
		$body = (json_decode($response->getBody()));
		if (isset($body->nice_id) && ($body->nice_id == $ticketID)){
			$ticket = new Ticket();
			$ticket->Key = $body->nice_id;
			$ticket->Summary = @$body->subject;
			$ticket->Priority = $this->priorityMap[@$body->priority_id];
			$ticket->Status = $this->statusMap[@$body->status_id];
			$ticket->Assignee = $this->getUser(@$body->assignee_id);
			if (isset($body->comments)){
				$lastComment = array_pop($body->comments);
				$ticket->LastComment = $lastComment->value;
			}
			return $ticket;
		}
	}

	public function getTickets($commits){
		$tickets = new ArrayList();
		foreach ($commits as $releaseID=>$commitMsg){
			preg_match_all('/'.$this->regex.'/', $commitMsg, $matches);//Debug::dump($matches);
			//TODO Cater for complex regex, with multiple tickets in the commit message
			if ($matches) {
				if (isset($matches[1])) {
					$revisionIds = $matches[1]; // If the regex included a group for isloating the revision id from a more complex pattern, ie extracting 1234 from the string HD#1234, using /HD#([0-9]+)
				} else {
					$revisionIds = $matches[0]; //Otherwise a simple look up for ticket id
				}
				foreach($revisionIds as $ticketID){
					$tickets->push($this->getTicket($ticketID));
				}
			}
		}
		return $tickets;
	}

	protected function getUser($userId){
		$response = $this->getClient()->request('/users/'.$userId.'.json');
		$body = (json_decode($response->getBody()));
		if (isset($body) && ($body->id == $userId)){
			return $body->name;
		}
		return 'N/A';
	}
}