<?php

class GitConnector implements ISCMSConnector {

	public function getSCMS(){
		exec("which git", $output, $errorCode);
		if ($errorCode === 0){
			if (count($output)) return $output[0];
		}
		throw new Exception('Invalid SCMS or not found.', $errorCode);
	}

	/**
	 * Return an array of commit messages
	 * @param Release $fromRelease The first commit of the release to consider, can be null
	 * @param Release $toRelease The last commit of the release
	 * @return array
	 */
	public function getCommits($fromRelease, Release $toRelease){
		try{
			$scsm = $this->getSCMS();
		} catch(Exception $e) {

		}
		if ($fromRelease && $fromRelease->exists()) {
			$fromTo = $fromRelease->SHA. '..' .$toRelease->SHA;
		} else {
			exec("$scsm log --reverse --pretty=tformat:'%H'", $firstCommit);
			$fromTo = $firstCommit[0] .' ' . $toRelease->SHA;
		}
		$logCmd = "$scsm log " . $fromTo . " --pretty=tformat:'%H%s'";
		exec($logCmd, $commits);

		$output = array();
		foreach ($commits as $commit){
			$output[substr($commit,0,40)] = substr($commit, 40);
		}
		return $output;
	}
}