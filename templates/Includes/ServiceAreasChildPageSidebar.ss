
<% with $getParentServiceAreasPage %>
	$SidebarContent
	<h3>Our Location<% if $ServiceAreasPageLocations.Count > 1 %>s<% end_if %></h3>
	<div id="small_map"><% include ServiceAreasPageMap %></div>
<% end_with %>

<% if $NeedsForm %>
	<div id="sap_form">
		<h3>Contact Us</h3>
		$ServiceAreasPageForm
	</div><!--sap_form-->
<% end_if %>
	
<% with $getParentServiceAreasPage %>
	$SidebarBottom
<% end_with %>
