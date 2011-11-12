<tr class="ss-gridfield-{$EvenOdd} $FirstLast">
	<% control Fields %>
	<td <% if FirstLast %>class="ss-gridfield-{$FirstLast}"<% end_if %>><a href="admin/reports/show/ReleaseReport?revid=$Value" class="popup-link">$Value</a></td>
	<% end_control %>
</tr>