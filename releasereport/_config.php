<?php

SS_Report::register("ReportAdmin", "ReleaseReport");

if (!defined('JIRA_REGEX')) define('JIRA_REGEX','myregextomatchinmycommitmessages'); //ie 'ABC-[0-9]*'
if (!defined('JIRA_BASE_URL')) define('JIRA_BASE_URL', 'https://example.onjira.com');
if (!defined('JIRA_USERNAME')) define('JIRA_USERNAME', 'username');
if (!defined('JIRA_PASSWORD')) define('JIRA_PASSWORD', 'password');

if (!defined('ZENDESK_REGEX')) define('ZENDESK_REGEX','myregextomatchinmycommitmessages'); // ie 'HD#([0-9]+)'
if (!defined('ZENDESK_BASE_URL')) define('ZENDESK_BASE_URL', 'https://example.zendesk.com');
if (!defined('ZENDESK_USERNAME')) define('ZENDESK_USERNAME', 'username');
if (!defined('ZENDESK_PASSWORD')) define('ZENDESK_PASSWORD', 'password');

ReleaseReport::$SCMSConnector = new GitConnector();

ProjectManagementConnectorManager::get()->register('Greenhopper', new JIRAGateway(JIRA_BASE_URL, JIRA_USERNAME, JIRA_PASSWORD, JIRA_REGEX));
ProjectManagementConnectorManager::get()->register('Zendesk', new ZendeskGateway(ZENDESK_BASE_URL, ZENDESK_USERNAME, ZENDESK_PASSWORD, ZENDESK_REGEX));