<?php if (!defined('THINK_PATH')) exit();?><div class="paging-total">总计 <b><?php echo ($total); ?></b> 条记录</div>
<div class="pagination pagination-right">
	<ul>
		<li><a href="#">上一页</a></li>
		<?php for($i=1;$i<=$pageNum;$i++){ ?>
			<li><a href="#"><?php echo $i ?></a></li>
		<?php } ?>
		<li><a href="#">下一页</a></li>
	</ul>
</div>