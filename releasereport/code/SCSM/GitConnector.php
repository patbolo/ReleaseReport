<?php

class GitConnector implements ISCMSConnector {

	/**
	 * Return an array of commit messages
	 * @param Release $fromRelease The first commit of the release to consider, can be null
	 * @param Release $toRelease The last commit of the release
	 * @return array
	 */
	public function getCommits($fromRelease, Release $toRelease){
		if ($fromRelease && $fromRelease->exists()) {
			$fromTo = $fromRelease->SHA. '..' .$toRelease->SHA;
		} else {
			exec("git log --reverse --pretty=tformat:'%H'", $firstCommit);
			$fromTo = $firstCommit[0] .' ' . $toRelease->SHA;
		}
		$logCmd = "git log " . $fromTo . " --pretty=tformat:'%H%s'";
		exec($logCmd, $commits);

		$output = array();
		foreach ($commits as $commit){
			$output[substr($commit,0,40)] = substr($commit, 40);
		}
		return $output;
	}
}