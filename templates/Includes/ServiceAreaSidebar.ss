<% if $isServiceChild %>

	<% with $getPointer %>$SidebarContent<% end_with %>
    <% with $getPointer %><h3>Our Location<% if $ServiceAreasLocations.Count > 1 %>s<% end_if %></h3><div id="small_map"><% include ServiceAreasMap %><% end_with %></div>
    <% if $NeedsForm %>
		<div id="sap_form">
			<h3>Contact Us</h3>
			$ServiceAreasForm
		</div><!--sap_form-->
	<% end_if %>
    <% with $getPointer %>$SidebarBottom<% end_with %>

<% else %>

    $SidebarContent
    <% if $NeedsForm %>
		<div id="sap_form">
			<h3>Contact Us</h3>
			$ServiceAreasForm
		</div><!--sap_form-->
	<% end_if %>
    $SidebarBottom

<% end_if %>