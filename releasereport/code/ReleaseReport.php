<?php

class ReleaseReport extends SS_Report{

	protected $dataClass = 'Release';

	static $SCMSConnector;

    function title(){
            return "Release history";
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

		

		$tickets = new ArrayList();
		$pmConnectorManager = ProjectManagementConnectorManager::get();

		$pmConnectors = $pmConnectorManager->getConnectors();
		if (!count($pmConnectors)) user_error('You need to define at least one source code management system connector');
		foreach ($pmConnectors as $pmConnector){
			$tickets->merge($pmConnector->getTickets($commits));
		}

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
		$grid = new GridField('ReportContent', 'ReportContent', new DataList($this->dataClass()), null);
		$grid->getPresenter()->setTemplate('ReleaseReportGridFieldPresenter');
		return $grid;
	}
}