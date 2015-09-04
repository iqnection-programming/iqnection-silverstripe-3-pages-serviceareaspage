    <section id="page_left">
    	<h1>$MenuTitle</h1>
        <% include ServiceAreasMap %>
    	$Content
        <% if Children %>
        	<div id="services_area">
                <div id="services_left" class="service_pages">
                    $PrintChildren(odd)
                </div><!--services_left-->
                <div id="services_right" class="service_pages">
                    $PrintChildren(even)
                </div><!--services_left-->
            </div><!--services_area-->
        <% end_if %>
    </section>
    <section id="page_right">
    	<% include ServiceAreaSidebar %>
    </section>