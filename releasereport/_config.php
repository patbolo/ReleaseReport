<?php

SS_Report::register("ReportAdmin", "ReleaseReport");

if (!defined('RELEASE_REPORT_REGEX')) define('RELEASE_REPORT_REGEX','myregextomatchinmycommitmessages');
if (!defined('RELEASE_REPORT_BASE_URL')) define('RELEASE_REPORT_BASE_URL', 'https://example.com');
if (!defined('RELEASE_REPORT_USERNAME')) define('RELEASE_REPORT_USERNAME', 'username');
if (!defined('RELEASE_REPORT_PASSWORD')) define('RELEASE_REPORT_PASSWORD', 'password');
ReleaseReport::$SCMSConnector = new GitConnector();
ReleaseReport::$ProjectManagementConnector = new JIRAConnector();