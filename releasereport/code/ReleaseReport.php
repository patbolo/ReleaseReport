<?php

class ReleaseReport extends SS_Report{

	protected $dataClass = 'Release';

	static $SCMSConnector;

	static $ProjectManagementConnector;

    function title(){
            return "Releases";
    }

	function forTemplate(){
	}

	/**
	 *
	 * @return FormField subclass
	 */
	function getReportField() {
		$vars = Controller::curr()->getRequest()->getVars();
		
		if(isset($vars['revid'])) return $this->getReleaseDetailField($vars['revid']);
		return $this->getReleasesField();
	}

	public function getReleaseDetailField($sha){
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
		$grid->getPresenter()->setTemplate('ReleaseReportDetailGridFieldPresenter');
		return $grid;
	}

	/**
	 *
	 * @return FormField subclass
	 */
	function getReleasesField() {
		return new GridField('ReportContent', 'ReportContent', new DataList($this->dataClass()), null, new ReleaseReportGridFieldPresenter());
	}
}