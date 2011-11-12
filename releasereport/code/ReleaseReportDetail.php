<?php

class ReleaseReportDetail_Controller extends Controller{

	static $SCMSConnector;

	static $ProjectManagementConnector;

	static $allowed_actions = array(
		'show'
	);

	public function show($request){
		if (!Member::currentUser() || !Permission::check('ADMIN')) return Director::set_status_code(401);
		$data = $request->requestVars();
		$sha = $data['SHA'];
		$latestRelease = DataList::create('Release')->where('"Release"."SHA" = \''.$sha.'\'')->First();
		if (!$latestRelease || !$latestRelease->exists()) user_error('Invalid release');

		$previousRelease = DataList::create('Release')->where('"Release"."Created" < \''.$latestRelease->Created.'\'')->sort('Created', "DESC")->First();

		if (!isset(self::$SCMSConnector)) user_error('You need to define a source code management system connector');

		$scsmConnector = self::$SCMSConnector;
		$commits = $scsmConnector->getCommits($previousRelease, $latestRelease);

		if (!isset(self::$ProjectManagementConnector)) user_error('You need to define a source code management system connector');

		$pmConnector = self::$ProjectManagementConnector;
		$tickets = $pmConnector->getTickets($commits);
		
		$grid = new GridField('ReportContent', 'ReportContent', $tickets);
		$grid->setModelClass('Ticket');
		return $grid->FieldHolder();
	}
}