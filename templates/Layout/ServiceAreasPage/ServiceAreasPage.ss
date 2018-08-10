<section id="page_left">
	<h1>$Title</h1>
	
	<% include ServiceAreasPageMap %>
	
	$Content
	
	<% if $Children %>
		<div id="services_area">
			<div id="services_left" class="service_pages">
				<ul class="level-1">
					<% loop $Children %>
						<% if $Odd %>
							<% include ServiceAreasPageChildListItem %>
						<% end_if %>
					<% end_loop %>
				</ul>
			</div><!--services_left-->
			<div id="services_right" class="service_pages">
				<ul class="level-1">
					<% loop $Children %>
						<% if $Even %>
							<% include ServiceAreasPageChildListItem %>
						<% end_if %>
					<% end_loop %>
				</ul>
			</div><!--services_left-->
		</div><!--services_area-->
	<% end_if %>
</section>
<section id="page_right">
	<% include ServiceAreasPageSidebar %>
</section>