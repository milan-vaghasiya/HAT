<?php $i=1; foreach($jobOutData as $row): ?>
<tr>
    <td><?=$i++?></td>
    <td><?=formatDate($row->entry_date)?></td>
    <td><?=$row->job_order_id?></td>
    <td><?=$row->entry_prefix.'/'.$row->entry_no?></td>
    <td><?=$row->product_id?></td>
    <td><?=$row->material_used_id?></td>
    <td><?=$row->process_id?></td>
    <td><?=$row->in_qty?></td>
    <td>&nbsp;</td>
    <td><?=$row->batch_no?></td>
    <td>&nbsp;</td>
    <td><?=$row->remark?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
<?php endforeach; ?>