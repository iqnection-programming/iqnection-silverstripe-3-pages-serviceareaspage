<li>
	<a href="$Link" $NavNoFollow>$MenuTitle</a>
	<% if $Children.Count %>
		<ul class="level-$ServiceAreasPageLevel">
			<% loop $Children %>
				<% include ServiceAreasPageChildListItem %>
			<% end_loop %>
		</ul>
	<% end_if %>
</li>