<h2>{$category.name}<span>{$category.short_desc}</span></h2>
{if $no_pages}
        <p>{$no_pages}</p>
{/if}
<ul>
{foreach $pages as $page}
	<li><em>{date('d.m.Y',$page.publish_date)}</em> <a href="{site_url($page.full_url)}">{$page.title}</a></li>
{/foreach}
</ul>
<div class="pagination" align="center">
    {$pagination}
</div>
