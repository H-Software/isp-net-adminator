<table class="table table-striped font-size-lg caption-top" style="font-size: 0.75em;">
  <caption>List of groups with tasks count for Queue-work</caption>
  <thead>
    <tr>
      <th scope="col">Id / GroupID</th>
      <th scope="col">workItem name</th>
      <th scope="col">number of tasks in group</th>
    </tr>
  </thead>
  <tbody>
    {if $work_list_groups_items|default:'0' }
        {foreach $work_list_groups_items as $i}
            <tr>
                <th scope="row">{$i@key}</th>
                <td>{$i.name|default: '' }</td>
                <td>{$i.count|default: '' }</td>
            </tr>
        {/foreach}
    {/if}
  </tbody>
</table>
