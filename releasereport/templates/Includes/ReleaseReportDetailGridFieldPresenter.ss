<% require css(sapphire/thirdparty/jquery-ui-themes/smoothness/jquery-ui.css) %>
<% require css(sapphire/css/GridField.css) %>

<h2>Tickets worked on</h2>
<div class="ss-gridfield ui-state-default" id="$Name">
	<table style="width:800px">
		<thead>
			<tr>
				<% control Headers %>
				<th class="<% if FirstLast %>ss-gridfield-{$FirstLast}<% end_if %><% if IsSortable %> ss-gridfield-sortable<% end_if %><% if IsSorted %> ss-gridfield-sorted ss-gridfield-{$SortedDirection}<% end_if %>">
					$Title <span class="ui-icon"></span></th>
				<% end_control %>
			</tr>
		</thead>

		<tbody>
			<% control Items %>
				<% include GridField_Item %>
			<% end_control %>
		</tbody>

		<tfoot>
		</tfoot>
	</table>

	<% control Footers %>
		$Render
	<% end_control %>

</div>