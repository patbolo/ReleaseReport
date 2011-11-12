<?php

interface ISCMSConnector{
	public function getCommits($fromRelease, Release $toRelease);
}